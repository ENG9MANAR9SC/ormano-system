<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AppointmentController;

/*
|--------------------------------------------------------------------------
| ADMIN Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:admin'])->group(function () {
  // users
  Route::get('users/export', [UserController::class, 'export']);
  Route::resource('users', UserController::class);
  Route::get('user/get-by-id/{id}', [UserController::class, 'searchById']);
  Route::get('user/{id}/sheets', [UserController::class, 'getUserSheets']);
  Route::post('user/sheet/save', [UserController::class, 'saveSheet']);
  Route::get('user/{id}/refresh-balance', [UserController::class, 'refreshBalance']);

  Route::post('sheet/{id}/add-file', [UserController::class, 'addFileToSheet']);
  Route::get('sheet/{id}/destroy', [UserController::class, 'destroySheet']);
  Route::post('user/notes', [UserController::class, 'saveNotes']);
  Route::get('user/packages', [UserController::class, 'getUserPackages']);



  // courts
  Route::resource('courts', CourtController::class);
  

  Route::resource('appointments', AppointmentController::class);
  Route::get('appointments/get/create/data', [AppointmentController::class, 'getCreateData']);
  Route::get('appointments/set-status/{id}', [AppointmentController::class, 'setStatus']);

  Route::get('admins/get/create/data', [AdminController::class, 'getCreateData']);
  Route::get('admins/{id}/set-active', [AdminController::class, 'setActive']);
  Route::resource('admins', AdminController::class);

  Route::get('getAdminData', [AdminController::class, 'getAdminData']);
  Route::resource('roles', RoleController::class);
  Route::post('/role/permission', [RoleController::class, 'togglePermission']);
  Route::resource('permissions', PermissionController::class);

  Route::get('orders', [OrderController::class, 'index']);
  Route::get('orders/export', [OrderController::class, 'export']);
  Route::get('orders/{id}', [OrderController::class, 'show']);

  Route::get('payments/export', [PaymentController::class, 'export']);
  Route::resource('payments', PaymentController::class);

  Route::resource('expenses', ExpenseController::class);
  Route::get('getCreateData', [ExpenseController::class, 'getCreateData']);


  Route::get('notes', [NoteController::class, 'index']);

  Route::get('home/data', [HomeController::class, 'getData']);

  //Reports
  Route::get('reports/devices', [ReportController::class, 'getDevices']);
  Route::get('reports/patients', [ReportController::class, 'getPatients']);
  Route::get('reports/appointments', [ReportController::class, 'getAppointments']);
  Route::get('reports/financials', [ReportController::class, 'getFinancials']);

  Route::get('reports/summeryFinancial', [ReportController::class, 'getSummeryFinancial']);
  Route::get('reports/financialThisMonth', [ReportController::class, 'getFinancialThisMonth']);

  Route::get('reports/sumeryFinancialThisYearly', [ReportController::class, 'getSumeryFinancialYearly']);
  Route::get('reports/sumeryFinancialThisMonth', [ReportController::class, 'getSumeryFinancialThisMonth']);
  Route::get('reports/summeryExpensesType', [ReportController::class, 'getSummeryExpensesType']);

  Route::get('reports/incomingByReferral', [ReportController::class, 'getIncomingByReferral']);
});
