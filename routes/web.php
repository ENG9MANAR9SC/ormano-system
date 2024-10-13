<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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

// Route::get('{any?}', function () {
// 	return view('application');
// })->where('any', '.*');

Route::get('/login', function () {
	return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
	$credentials = $request->only('email', 'password');
	// Attempt to authenticate the user
	if (auth()->guard('admin')->attempt($credentials)) {
		// Check if the user is enabled
		$user = auth()->guard('admin')->user();
		if ($user && !$user->enabled) {
			return redirect()->back()->withErrors([
				'msg' => 'Your account is not enabled.'
			]);
		}

		return redirect('/');
	}

	return redirect()->back()->withErrors([
		'msg' => 'Invalid email or password'
	]);
})->name('login');



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
