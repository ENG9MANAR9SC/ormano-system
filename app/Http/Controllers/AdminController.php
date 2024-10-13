<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
  //
  public function index()
  {

    $per_page = request()->per_page ?? 12;
    $search_key = request()->s ?? null;
    $is_supervisor = request()->is_supervisor ?? false;

    $admins = Admin::when(isset($search_key), function ($q) use ($search_key) {
      $q->where('name', 'LIKE', '%' . $search_key . '%');
    })->when($is_supervisor, function ($q) {
      $q->whereHas('roles.permissions', function ($query) {
        $query->where('name', 'other_has_supervision');
      });
    });

    // dd($admins->toSql(), $admins->getBindings());

    $admins = $admins->with('roles')->paginate($per_page);

    return response()->json([
      'admins' => $admins,
    ]);
  }

  public function getAdminData()
  {

    $admin = Auth::guard('admin')->user();

    return response()->json([
      'admin' => $admin,
      'permissions' => $admin->getAllPermissions()->pluck('name'),

    ]);
  }

  public function store()
  {
    if (request()->id) { // update
      $data = request()->validate([
        'id'        => 'required|exists:admins,id',
        'name'      => 'required',
        'email'     => 'required|email|unique:admins' . (request()->id ? ',email,' . request()->id : ''),
        'phone'     => 'unique:admins' . (request()->id ? ',phone,' . request()->id : ''),
        'password'  => 'nullable|min:6|same:confirm_password',
        'role'      => 'required',
      ]);
    } else { // create
      $data = request()->validate([
        'name'      => 'required',
        'email'     => 'required|email|unique:admins',
        'phone'     => 'unique:admins',
        'password'  => 'required|min:6|same:confirm_password',
        'role'      => 'required',
      ]);
    }

    if (isset($data['id'])) {
      $admin = Admin::find($data['id']);

      $admin->update([
        'name'      => $data['name'],
        'email'     => $data['email'],
        'phone'     => isset($data['phone']) ? $data['phone'] : null,
        // 'password'  => bcrypt($data['password']),
      ]);

      if (isset($data['password'])) {
        $admin->update([
          'password'  => bcrypt($data['password']),
        ]);
      }

      $admin->syncRoles($data['role']);
    } else {
      $admin = Admin::create([
        'name'      => $data['name'],
        'email'     => $data['email'],
        'phone'     => $data['phone'],
        'password'  => bcrypt($data['password']),
      ]);

      $admin->assignRole($data['role']);
    }

    return response()->json([
      'admin' => $admin,
      'status' => true,
    ]);
  }

  public function show($id)
  {
    $admin = Admin::find($id);

    $admin->role = $admin->getRoleNames()[0];

    return response()->json([
      'admin' => $admin,
    ]);
  }

  public function destroy($id)
  {
    $admin = Admin::find($id);

    $admin->delete();

    return response()->json([
      'status' => true,
    ]);
  }

  public function setActive($id)
  {
    $admin = Admin::find($id);

    $admin->update([
      'enabled' => request()->status,
    ]);

    return response()->json([
      'status' => true,
    ]);
  }

  public function getCreateData()
  {
    $roles = Role::all();

    return response()->json([
      'roles' => $roles,
    ]);
  }
}
