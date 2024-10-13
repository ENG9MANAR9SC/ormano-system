<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Type;
use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Device;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\AppModel;
use App\Models\Referral;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
  //

  public function getDevices()
  {

    $device_ids = DB::table('appointment_device')
      ->join('appointments', 'appointment_device.appointment_id', '=', 'appointments.id')
      ->where('appointments.status', '<>', Appointment::STATUS_CANCELLED)
      ->pluck('appointment_device.device_id')
      ->countBy()
      ->toArray();

    $labels = [];
    $series = [];

    foreach ($device_ids as $deviceId => $count) {
      $device = Device::find($deviceId);
      if ($device) {
        $title = $device->title;
        $labels[] = $title;
        $series[] = $count;
      }
    }
    $chartData = [
      'labels' => $labels,
      'series' => $series,
    ];

    // $devices_data = Device::whereIn('id',$device_ids)->get();
    return response()->json([
      // 'device_ids' => $device_ids,
      // 'devices_data' => $devices_data,
      'chartData' => $chartData,
      'status'  => true,
    ]);
  }


  public function getPatients()
  {
    $perPage = request()->per_page ?? 12;
    $userIds = User::where('active', 1)->pluck('id');

    $userAppointments = Appointment::whereIn('user_id', $userIds)->whereNot('status', Appointment::STATUS_CANCELLED)->count();

    $labels = ['Appointments'];
    $series = [$userAppointments];

    $chartData = [
      'labels' => $labels,
      'series' => $series,
    ];

    $topUsers = User::select(
      'users.id',
      'users.full_name',
      DB::raw('(SELECT COUNT(*) FROM appointments WHERE appointments.user_id = users.id AND appointments.status = 2) as appointment_count'),
      DB::raw('(SELECT COALESCE(SUM(total_paid), 0) FROM orders WHERE orders.user_id = users.id) as total_paid'),
      DB::raw('(SELECT COALESCE(SUM(total_remaining), 0) FROM orders WHERE orders.user_id = users.id) as total_remaining'),
      DB::raw('(SELECT COALESCE(SUM(grand_total), 0) FROM orders WHERE orders.user_id = users.id) as grand_total')
    )
      ->orderByDesc('appointment_count')
      ->paginate($perPage);

    return response()->json([
      'chartData' => $chartData,
      'topUsers' => $topUsers,
      'status'  => true,
    ]);
  }


  public function getAppointments()
  {
    ///monthly and yearly
    $currentYear = now()->year;
    $years = range($currentYear - 6, $currentYear);
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    $monthSeriesData = [];
    $yearSeriesData = [];

    foreach ([Appointment::TYPE_SESSION, Appointment::TYPE_VISIT] as $appointmentType) {
      $typeData = [
        'name' => Appointment::TYPE_ARRAY[$appointmentType],
        'data' => [],
      ];
      // Monthly data for the last 12 months
      for ($i = 0; $i < 12; $i++) {
        $month = now()->subMonths($i);
        $appontmentCount = Appointment::where('status', Appointment::STATUS_COMPLETED)
          ->where('type', $appointmentType)
          ->whereYear('date', $month->year)
          ->whereMonth('date', $month->month)
          ->count();

        $typeData['data'][] = $appontmentCount;
      }
      $monthSeriesData[] = $typeData;

      $typeData = [
        'name' => Appointment::TYPE_ARRAY[$appointmentType],
        'data' => [],
      ];
      foreach ($years as $year) {
        $appontmentCount = Appointment::where('status', Appointment::STATUS_COMPLETED)
          ->where('type', $appointmentType)
          ->whereYear('date', $year)
          ->count();
        $typeData['data'][] = $appontmentCount;
      }
      $yearSeriesData[] = $typeData;
    }

    // Yearly data for the last seven years
    $yearlyData = [
      'name' => 'Yearly',
      'data' => [],
    ];

    // $seriesData[] = $yearlyData;

    return response()->json([
      'series' => [
        'months' => $monthSeriesData,
        'years' => $yearSeriesData
      ],
      'xaxis' => [
        'months' => $months,
        'years' => $years,
      ],
    ]);
  }

  public function getFinancials()
  {
    $currentYear = now()->year;
    $years = range($currentYear - 6, $currentYear);
    // $months = ['Jan' ,'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $months = [];

    $seriesMonth = [
      'payment' => [
        'name' => 'payment',
        'data' => [],
      ],
      'expenses' => [
        'name' => 'expenses',
        'data' => [],
      ],
    ];
    $seriesYear = [
      'payment' => [
        'name' => 'payment',
        'data' => [],
      ],
      'expenses' => [
        'name' => 'expenses',
        'data' => [],
      ],
    ];

    // Monthly data for the last 12 months
    for ($i = 0; $i < 12; $i++) {
      $month = now()->subMonths($i);

      // Monthly data for the last 12 months
      array_unshift($months, $month->format('M'));

      $financialAmount = Payment::whereYear('created_at', $month->year)
        ->whereMonth('created_at', $month->month)
        ->sum('amount');
      array_unshift($seriesMonth['payment']['data'], $financialAmount);

      $expenseAmount = Expense::whereYear('expense_date', $month->year)
        ->whereMonth('expense_date', $month->month)
        ->sum('amount');

      array_unshift($seriesMonth['expenses']['data'], $expenseAmount);
    }

    // Yearly data for the last seven years
    foreach ($years as $year) {
      $financialAmount = Payment::whereYear('created_at', $year)->sum('amount');
      $expenseAmount = Expense::whereYear('created_at', $year)->sum('amount');

      array_push($seriesYear['payment']['data'], $financialAmount);
      array_push($seriesYear['expenses']['data'], $expenseAmount);
    }

    return response()->json([
      'series' => [
        'months' => $seriesMonth,
        'years' => $seriesYear,
      ],
      'xaxis' => [
        'months' => $months,
        'years' => $years,
      ],
    ]);
  }
  //payment and expenses money
  public function getSummeryFinancial()
  {
    $unPaidSummery = Order::sum('total_remaining');
    $paidSummery   = Payment::sum('amount');

    $labels = ['Unpaid', 'Paid'];
    $series = [$unPaidSummery, $paidSummery]; // Populate the series array

    $chartData = [
      'labels' => $labels,
      'series' => $series,
    ];

    return response()->json([
      'chartData' => $chartData,
      'status'    => true,
    ]);
  }

  //paid and unpaid money for this month
  public function getFinancialThisMonth()
  {
    $currentMonth   = now()->month;
    $unPaidSummery  = Order::whereMonth('created_at', $currentMonth)->sum('total_remaining');
    $paidSummery    = Payment::whereMonth('created_at', $currentMonth)->sum('amount');

    $labels = ['Unpaid', 'Paid'];
    $series = [$unPaidSummery, $paidSummery];

    $chartData = [
      'labels' => $labels,
      'series' => $series,
    ];

    return response()->json([
      'chartData' => $chartData,
      'status' => true,
    ]);
  }

  //payment and expenses money

  public function getSumeryFinancialYearly()
  {
    $financialAmount  = Payment::sum('amount');
    $expenseAmount   = Expense::sum('amount');

    $labels = ['Payments', 'Expenses'];
    $series = [$financialAmount, $expenseAmount];

    $chartData = [
      'labels' => $labels,
      'series' => $series,
    ];

    return response()->json([
      'chartData' => $chartData,
      'status' => true,
    ]);
  }
  //payment and expenses money for this month
  public function getSumeryFinancialThisMonth()
  {
    $currentMonth     = Carbon::now()->month;
    $financialAmountMonth  = Payment::whereMonth('created_at', $currentMonth)->sum('amount');
    $expenseAmountMonth    = Expense::whereMonth('expense_date', $currentMonth)->sum('amount');

    $financialAmountAll  = Payment::sum('amount');
    $expenseAmountAll    = Expense::sum('amount');

    $labels = ['Payments', 'Expenses'];

    $monthSeries = [$financialAmountMonth, $expenseAmountMonth];
    $allSeries = [$financialAmountAll, $expenseAmountAll];

    $monthData = [
      'labels' => $labels,
      'series' => $monthSeries,
    ];
    $allData = [
      'labels' => $labels,
      'series' => $allSeries,
    ];

    return response()->json([
      'this_month' => $monthData,
      'all_time' => $allData,
      'status' => true,
    ]);
  }

  // summery types of expenses
  public function getSummeryExpensesType()
  {
    $app_model    = AppModel::where('slug', 'expense')->pluck('id');
    $expenseTypes = Type::where('app_model_id', $app_model)->pluck('slug')->toArray();

    $labels = [];
    $series = [];


    foreach ($expenseTypes as $expenseType) {
      $expenseAmount = Expense::where('type', Type::where('slug', $expenseType)->first()->id)->sum('amount');

      $labels[] = ucwords(str_replace('-', ' ', $expenseType)); // Convert slug to title case
      $series[] = $expenseAmount;
    }


    $chartData = [
      'labels' => $labels,
      'series' => $series,
    ];

    return response()->json([
      'chartData' => $chartData,
      'status'    => true,
    ]);
  }

  public function getIncomingByReferral()
  {
    // get all referrals
    $users = User::whereNotNull('referral_model')
      ->whereNotNull('referral_id')
      ->with('payments')
      ->get();

    $paymentsGroupedByReferral = $users->flatMap(function ($user) {
      return $user->payments->groupBy(function ($payment) use ($user) {
        $app_model = AppModel::find($user->referral_model);

        switch ($app_model->slug) {
          case 'user':
            $user = User::find($user->referral_id);
            $group_name = $user->full_name . ' (Patient)';
            break;
          case 'referral':
            $referral = Referral::find($user->referral_id);
            $group_name = $referral->title . ' (Custom)';
            break;
          case 'admin':
            $admin = Admin::find($user->referral_id);
            $group_name = $admin->name . ' (Employee)';
            break;
          default:
            return abort(400);
        }
        return $group_name;
      });
    });

    $sumOfPaymentsByGroup = $paymentsGroupedByReferral->map(function ($groupedPayments) {
      return $groupedPayments->sum('amount');
    });

    return response()->json([
      'data' => $sumOfPaymentsByGroup,
      'status' => true,
    ]);
  }
}
