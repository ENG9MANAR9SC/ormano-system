<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $Permissions =  [
      // [1, "SHOW_LOGS" ,"MONITORING"],
      // [2, "ODOO_ORDER_LOGS" , "MONITORING"],
      // [3, "ODOO_PAYMENT_LOGS" , "MONITORING"]
      ["category_index"],
      ["category_create"],
      ["category_edit"],
      ["category_delete"],

      ["device_index"],
      ["device_create"],
      ["device_edit"],
      ["device_delete"],

      ["appointment_index"],
      ["appointment_create"],
      ["appointment_show"],
      ["appointment_edit"],
      ["appointment_delete"],

      ["pateint_index"],
      ["pateint_show"],
      ["pateint_create"],
      ["pateint_edit"],
      ["pateint_delete"],
      ["pateint_export"],

      ["pateint_sheet_show"],
      ["pateint_sheet_edit"],
      ["pateint_sheet_add"],
      ["pateint_sheet_delete"],
      ["pateint_sheet_delete_file"],

      ["pateint_show_balance"],

      ["pateint_medical_info_show"],
      ["pateint_medical_info_edit"],
      ["pateint_medical_info_add"],
      ["pateint_medical_info_delete"],

      ["admin_index"],
      ["admin_show"],
      ["admin_create"],
      ["admin_edit"],
      ["admin_delete"],

      ["role_index"],
      ["role_show"],
      ["role_create"],
      ["role_edit"],
      ["role_delete"],

      ["invoice_index"],
      ["invoice_show"],

      ["payment_index"],
      ["payment_add"],
      ["payment_invoice_show"],
      ["payment_export"],

      ["expense_index"],
      ["expense_create"],
      ["expense_edit"],
      ["expense_delete"],

      ['referral_index'],
      ['referral_create'],
      ['referral_show'],
      ['referral_delete'],
      ['referral_edit'],
      ['referral_assign'],

      ['other_has_supervision'],

      ['report_general'],
      ['report_finacial'],
    ];

    foreach ($Permissions as $permission) {
      // $p = Permission::findByName($permission[1], 'admin');
      $exists = Permission::where('name', $permission[0])->exists();
      if (!$exists) {
        $per = Permission::create([
          'name'       => $permission[0],
          'guard_name' => 'admin'
        ]);
        DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [$per->id, 1]);
      }
    }
  }
}
