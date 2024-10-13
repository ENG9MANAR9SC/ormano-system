<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Models\Admin;
use App\Models\Expense;
use App\Models\AppModel;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
  //
  public function index()
  {
    $per_page = request()->per_page ?? 12;
    $expenses = Expense::paginate($per_page);

    return response()->json([
      'expenses' => $expenses,

    ]);
    //
  }

  public function getCreateData()
  {
    $app_model    = AppModel::where('slug', 'expense')->pluck('id');
    $expenseTypes = Type::where('app_model_id', $app_model)->get();
    $admins = Admin::get();
    return response()->json([
      'types' => $expenseTypes,
      'admins' => $admins,

    ]);
  }

  public function store(Request $request)
  {

    $validated = $request->validate([
      'id' => 'nullable',
      'amount' => 'required',
      'admin_id' => 'required',
      'type_id' => 'required',
      'date' => 'required',
      'note' => 'nullable',
    ]);

    Expense::createOrUpdate($validated, $request->id);

    return response()->json([
      'status' => true,
    ]);
  }



  public function show(Expense $expense)
  {
    return response()->json([
      'item' => $expense,
    ]);
  }


  public function edit(Expense $expense)
  {
    //
  }


  public function update(Request $request, Expense $expense)
  {
    //
  }

  public function destroy(Expense $expense)
  {
    Expense::destroy($expense->id);

    return true;
  }
}
