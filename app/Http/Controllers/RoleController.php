<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    //
    public function index() {
      $roles = Role::all();
      return response()->json([
        'roles' => $roles,
      ]);
    }

    public function show($id) {
      $role = Role::find($id);
      $role->load('permissions');

      return response()->json([
        'role' => $role,
      ]);
    }

    public function togglePermission(Request $request) {
      $input = $request->validate([
        'role_id' => 'required',
        'permission' => 'required',
        'checked' => 'required',
      ]);

      $role = Role::findById($input['role_id']);

      if($input['checked']) {
        $role->givePermissionTo($input['permission']);
      } else {
        $role->revokePermissionTo($input['permission']);
      }

      return response()->json([
        'status' => true,
      ]);

    }
}
