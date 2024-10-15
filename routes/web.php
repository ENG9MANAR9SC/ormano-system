<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', function () {
	return view('login');
})->name('login');


Route::post('login', [AuthController::class, 'checkLogin']);



// replace with line below to disable middleware
// Route::middleware([])
Route::middleware(['auth:admin'])->group(function () {

	Route::get('/', function () {
		return view('application');
	});
	Route::get('/logout', function () {
		auth()->guard('admin')->logout();
		return redirect('/login');
	});
	// Route::get('categories/getData', [CategoryController::class, 'getData']);
	// Route::post('categories/createOrUpdate', [CategoryController::class, 'createOrUpdate']);
	Route::get('{any}', function () {
		return view('application');
	})->where('any', '.*');
});
