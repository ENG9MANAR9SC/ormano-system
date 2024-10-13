<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\ExcelExporterService;

class PaymentController extends Controller
{
  public function index()
  {
    $search_key = request()->s;
    $per_page = request()->per_page;

    $payments = Payment::whereHas('user', function ($q) use ($search_key) {
      $q->where('full_name', 'LIKE', '%' . $search_key . '%');
    })->paginate($per_page);

    return response()->json([
      'payments'  => $payments,
    ]);
  }

  public function show($id)
  {
    $payment = Payment::findOrfail($id);

    return response()->json([
      'payments'  => $payment,
    ]);
  }


  public function store(Request $request)
  {
    $validated = $request->validate([
      "user_id" => "required",
      "currency" => "required",
      "amount" => "required",
      "notes" => "nullable",
    ]);
    $notes = $validated['notes'] ?? '';
    $payment = OrderService::getInstance()
      ->setUserId($validated['user_id'])
      ->setCurrency($validated['currency'])
      ->setAmount($validated['amount'])
      ->setNotes($notes)
      ->createUserPayment();

    return response()->json([
      'status'  => true,
    ]);
  }

  public function export()
  {
    $payments = Payment::all();

    $headers = [
      'ID',
      'Patient name',
      'Amount',
      'Created by',
      'Referral',
      'Date',
    ];

    $rows = $payments->map(function ($payment) {
      $user_referral = User::find($payment->user_id)->referral_details;

      return [
        $payment->id,
        $payment->user_name,
        $payment->amount,
        $payment->created_by_name,
        $user_referral,
        $payment->created_at->format('Y-m-d H:i:s'),
      ];
    })->toArray();


    $result = ExcelExporterService::getInstance()
      ->setHeaders($headers)
      ->setRows($rows)
      ->exportExcel();

    return response()->json([
      'filedata' => $result['file'],
      'filename' => $result['name'],
    ]);
  }
}
