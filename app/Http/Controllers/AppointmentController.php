<?php

namespace App\Http\Controllers;

use App\Enums\DiscountType;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Device;
use App\Models\AppModel;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $status = request()->status ?? null;
    $type = request()->type ?? null;
    $supervisor_id = request()->supervisor_id ?? null;
    $user_id = request()->user_id ?? null;

    $appointments = Appointment::when(isset($status), function ($q) use ($status) {
      $q->where('status', $status);
    })
      ->when(isset($type), function ($q) use ($type) {
        $q->where('type', $type);
      })
      ->when(isset($supervisor_id), function ($q) use ($supervisor_id) {
        $q->where('supervisor_id', $supervisor_id);
      })
      ->when(isset($user_id), function ($q) use ($user_id) {
        $q->where('user_id', $user_id);
      });

    if (isset(request()->start_date) && isset(request()->end_date)) {
      $appointments = $appointments->whereBetween('date', [
        Carbon::parse(request()->start_date),
        Carbon::parse(request()->end_date)
      ])
        ->with('user')
        ->with('devices')
        ->get();
    } else {
      $appointments = $appointments->with('users')->with('devices')->get();
    }

    return response()->json([
      'appointments' => $appointments,
    ]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

    $validated = $request->validate([
      'date'      => 'required',

      'type'      => 'required',
      'package_id'      => 'required_if:type,3',

      'user_id'   => 'required',
      'time'      => 'required',
      'pricing'   => 'required',
      'duration'  => 'required',
      'notes'     => 'nullable',
      'devices'   => 'nullable',

      'cost'      => 'nullable',
      'discount'  => 'nullable',
      'discount_type'  => 'nullable',

      'currency' => 'nullable',

      'initial_payment_amount' => 'nullable',
      'initial_payment_currency' => 'nullable',

      'supervisor_id' => 'nullable',
    ]);

    $appointment = Appointment::createOrUpdate($validated, $request->id);

    if (isset($validated['initial_payment_amount'])) {
      $payment = OrderService::getInstance()
        ->setUserId($validated['user_id'])
        ->setCurrency($validated['initial_payment_currency'])
        ->setAmount($validated['initial_payment_amount'])
        ->setNotes('Added in #' . $appointment->id . ' appointment')
        ->createUserPayment();
    }

    return response()->json([
      'status' => true,
    ]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Appointment  $appointment
   * @return \Illuminate\Http\Response
   */
  public function show(Appointment $appointment)
  {
    $appointment = Appointment::where('id', $appointment->id)->with('devices')->first();
    return response()->json([
      'appointment' => $appointment,
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Appointment  $appointment
   * @return \Illuminate\Http\Response
   */
  public function edit(Appointment $appointment)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Appointment  $appointment
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Appointment $appointment)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Appointment  $appointment
   * @return \Illuminate\Http\Response
   */
  public function destroy(Appointment $appointment)
  {
    //

    Appointment::destroy($appointment->id);

    return true;
  }

  public function getCreateData()
  {
    $devices = Device::all();

    return response()->json([
      'devices' => $devices,
    ]);
  }

  public function setStatus($id)
  {
    request()->validate([
      'status' => 'required',
    ]);
    $appointment = Appointment::find($id);

    $appointment->status = request()->status;
    $appointment->save();

    if ($appointment->status == Appointment::STATUS_CANCELLED) {
      ////////////////////////
      // revert order
      ////////////////////////
      $order = Order::where('orderable_id', $appointment->id)->where('orderable_type', Appointment::class)->first();
      if ($order) {
        $order->status = Order::STATUS_CANCELED;

        $returnedAmount = $order->total_paid;

        $order->total_paid = 0;
        $order->total_remaining = $order->grand_total;

        $order->save();

        $orders = Order::where('user_id', $appointment->user_id)
          ->whereIn('status', [Order::STATUS_UNPAID, Order::STATUS_PARTIAL_PAID])
          ->orderBy('created_at')
          ->get();

        foreach ($orders as $key => $order) {
          $deducted = min($returnedAmount, $order->total_remaining);

          $order->total_remaining -= $deducted;
          $order->total_paid += $deducted;
          $returnedAmount -= $deducted;

          $order->save();
          if ($returnedAmount == 0) {
            break;
          }
        }

        $user = User::find($order->user_id);
        $user->recalculateBalance();
      }
    }

    if ($appointment->status == Appointment::STATUS_COMPLETED) {
      // TODO: if appointment is:
      // ? visit    add fees  object with (cost - discount - currency)
      // ? session  add items object with (quantity - price - currency - discount - exchange_rate) which represents devices


      $order = OrderService::getInstance()
        ->setModelId($appointment->id)
        ->setModelType(Appointment::class)
        ->setType($appointment->type == Appointment::TYPE_VISIT ? Order::TYPE_APPOINTMENT_VISIT : Order::TYPE_APPOINTMENT_SESSION)
        ->setUserId($appointment->user_id);

      // create visit order
      if ($appointment->type == Appointment::TYPE_VISIT) {

        // subtotal is 0 because visit charges are considered as fees
        $sub_total = 0;
        if ($appointment->discount_type == DiscountType::PERCENTAGE) {
          $grand_total = $appointment->cost - ($appointment->cost * ($appointment->discount / 100));
        } else if ($appointment->discount_type == DiscountType::ABSOLUTE) {
          $grand_total = $appointment->cost - $appointment->discount;
        }

        $order = $order->setFees([[
          'price'     => isset($appointment->cost) ? $appointment->cost : 0,
          'fee_type'  => Order::FEE_TYPE_VISIT,
          'fee_title' => Order::FEE_TYPES_ARRAY[Order::FEE_TYPE_VISIT],
          'discount'  => $appointment->discount,
          'discount_type'  => $appointment->discount_type,
          'currency'  => isset($appointment->currency) ? $appointment->currency : null,
        ]])
          ->setSubtotal($sub_total)
          ->setGrandtotal($grand_total);
      } else if ($appointment->type == Appointment::TYPE_SESSION) {
        $items = $appointment->devices;
        $sub_total = 0;
        $grand_total = 0;

        // ? model
        // ? quantity
        // ? price
        // ? currency
        // ? discount
        // ? exchange_rate
        foreach ($items as $item) {
          $sub_total += $item->price;
          if ($item->pivot->discount_type == DiscountType::PERCENTAGE) {
            $grand_total += $item->price - ($item->price * $item->pivot->discount / 100);
          } elseif ($item->pivot->discount_type == DiscountType::ABSOLUTE) {
            $grand_total += $item->price - $item->pivot->discount;
          }
        }
        $order->setSubtotal($sub_total)
          ->setGrandtotal($grand_total);
      }
      if ($grand_total > 0) {
        $order->createOrder();
      }
    }

    return response()->json([
      'status' => true,
    ]);
  }
}
