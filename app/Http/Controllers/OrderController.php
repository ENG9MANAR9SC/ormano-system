<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\ExcelExporterService;

class OrderController extends Controller
{
  public function index(Request $request)
  {
    $per_page = $request->per_page;
    $status = $request->status;
    $search_key = $request->search_key;

    // $type =
    // where('orderable_id', $request->type)->
    $orders = Order::when($status != null, function ($query) use ($status) {
      return $query->where('status', $status);
    });

    if ($search_key) {
      $orders = $orders->where('id', 'LIKE', '%' . $search_key . '%')
        ->orWhereHas('user', function ($q) use ($search_key) {
          $q->where('full_name', 'LIKE', '%' . $search_key . '%');
        });
    }



    return response()->json([
      'orders' => $orders->paginate($per_page),
    ]);
  }

  public function show($id)
  {
    $order = Order::find($id);
    $items = $order->getItems();

    return response()->json([
      'order' => $order,
      'items' => $items,
    ]);
  }

  public function export()
  {
    $orders = Order::all();

    $headers = [
      "ID",
      "Type Name",
      "Status Name",
      "User ID",
      "User Name",
      "Sub Total",
      "Grand Total",
      "Total Paid",
      "Total Remaining",
      "Grand Total Fees",
      "Sub Total Fees",
      "Notes",
      "Created At",
    ];



    $rows = $orders->map(function ($order) {
      return [
        $order->id,
        $order->type_name,
        $order->status_name,
        $order->user_id,
        $order->user_name,
        $order->sub_total,
        $order->grand_total,
        $order->total_paid,
        $order->total_remaining,
        $order->grand_total_fees,
        $order->sub_total_fees,
        $order->notes,
        $order->created_at,
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
