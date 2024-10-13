<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
  //
  public function index()
  {

    // $roleId = request()->id;
    $permissions = Permission::all();
    // $rolesWithPermissions = Role::with('permissions')->get();
    return response()->json([
      'permissions' => $permissions,
      // 'rolesWithPermissions' => $rolesWithPermissions,

    ]);
  }
  public function checkPermission(Request $request)
  {

    if ($request->admin_id) {
      $admin = Admin::findOrFail($request->admin_id);

      if ($admin->hasAnyPermission($request->permission)) {
        return true;
      } else {
        return false;
      }
    } elseif (auth()->guard('admin')->user()->hasAnyPermission($request->permission)) {
      return true;
    } else {
      return false;
    }
  }
}
