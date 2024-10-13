<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  //
  public function checkLogin()
  {

    $credentials = request()->only(['email', 'password']);
    $remember = request()->has('remember');

    $user = Admin::where('email', $credentials['email'])->first();

    if ($user == null) {
      return redirect()->back()->withErrors(
        [
          'status' => '403',
          'data' => array(),
          'msg' => 'invalid email'
        ]
      );
    } else {

      if ($user->enabled == 0) {
        return redirect()->back()->withErrors(
          [
            'status' => '403',
            'data' => array(),
            'msg' => 'User Inactive'
          ]
        );
      }
      //Remember me
      if ($remember) {
        // Set the "remember me" session
        request()->session()->put('remember_me', true);
      }
      // Hash::check($credentials['password'], '$2a$12$2yZyr1I9DTlRNwBii57fGekp65BuozVtCzGITnANaW2JiTRN/n9NC')
      if (Hash::check($credentials['password'], $user->password)) {

        if (Auth::guard('admin')->login($user)) {
          return redirect()->back()->withErrors(
            [
              'status' => '403',
              'data' => array(),
              'msg' => 'invalid_email_or_password'
            ]
          );
        }
      } else {
        return redirect()->back()->withErrors([
          'status' => '403',
          'data' => array(),
          'msg' => 'invalid_password'
        ]);
      }

      $remember = request()->remember == 1 ? true : false;

      $user['token'] = auth()->guard('admin')->attempt($credentials, $remember);
      $user['token_type'] = 'bearer';
      return redirect()->route('/dashboard');
    }
  }

  public function logout()
  {
    // Auth::user()->tokens()->delete();
    auth()->guard('admin')->logout();

    return redirect()->route('test-admin');
  }
}
