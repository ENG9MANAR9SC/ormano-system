<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admins
        $admins_data = [
            'id' => 1,
            'name' => 'admin',
            'email' => 'admin@admin.net',
            'phone' => NULL,
            'password' => '$2a$12$csrh5ZNvR.sWaME2UShpCOe0Rbx1JxVXi2wAdbwacFDEj9Wcy6rqG', // password
            'remember_token' => NULL,
            'avatar' => NULL,
            'enabled' => 1,
            'deleted_at' => NULL,
            'created_at' => '2024-05-12 21:20:12',
            'updated_at' => '2024-05-12 21:20:13',
        ];
        DB::table('admins')->insert($admins_data);

        // app models
        $app_models_data = [
            [
                'title' => 'Device',
                'slug' => 'device',
                'description' => 'Device',
                'path' => 'App\Models\Device',
                'status' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2023-10-21 20:38:24',
                'updated_at' => '2023-10-21 20:38:25',
            ],
            [
                'title' => 'User',
                'slug' => 'user',
                'description' => 'user',
                'path' => 'App\Models\User',
                'status' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2023-10-27 15:54:35',
                'updated_at' => '2023-10-27 15:54:36',
            ],
            [
                'title' => 'Appointment',
                'slug' => 'appointment',
                'description' => 'Appointment',
                'path' => 'App\Models\Appointment',
                'status' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2023-11-22 00:17:23',
                'updated_at' => '2023-11-22 00:17:24',
            ],
            [
                'title' => 'Referral',
                'slug' => 'referral',
                'description' => 'Referral',
                'path' => 'App\Models\Referral',
                'status' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2023-11-22 00:17:23',
                'updated_at' => '2023-11-22 00:17:24',
            ],
            [
                'title' => 'Admin',
                'slug' => 'admin',
                'description' => 'Admin',
                'path' => 'App\Models\Admin',
                'status' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2023-11-22 00:17:23',
                'updated_at' => '2023-11-22 00:17:24',
            ],
            [
                'title' => 'Expense',
                'slug' => 'expense',
                'description' => 'expense',
                'path' => 'App\Models\Expense',
                'status' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2023-12-26 19:35:22',
                'updated_at' => '2023-12-26 19:35:22',
            ],
        ];
        DB::table('app_models')->insert($app_models_data);

        // types
        $types_data = [
            [
                'title' => 'Aesthetic',
                'slug' => 'aesthetic',
                'descriptions' => NULL,
                'properties' => NULL,
                'status' => 1,
                'app_model_id' => 7,
                'attribute_set_id' => NULL,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'title' => 'Tmc',
                'slug' => 'tmc',
                'descriptions' => NULL,
                'properties' => NULL,
                'status' => 1,
                'app_model_id' => 7,
                'attribute_set_id' => NULL,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'title' => 'Lab',
                'slug' => 'lab',
                'descriptions' => NULL,
                'properties' => NULL,
                'status' => 1,
                'app_model_id' => 7,
                'attribute_set_id' => NULL,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'title' => 'Pharmacy',
                'slug' => 'pharmacy',
                'descriptions' => NULL,
                'properties' => NULL,
                'status' => 1,
                'app_model_id' => 7,
                'attribute_set_id' => NULL,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'title' => 'General expenses',
                'slug' => 'general-expenses',
                'descriptions' => NULL,
                'properties' => NULL,
                'status' => 1,
                'app_model_id' => 7,
                'attribute_set_id' => NULL,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'title' => 'Salaries',
                'slug' => 'salaries',
                'descriptions' => NULL,
                'properties' => NULL,
                'status' => 1,
                'app_model_id' => 7,
                'attribute_set_id' => NULL,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'title' => 'Payments',
                'slug' => 'payments',
                'descriptions' => NULL,
                'properties' => NULL,
                'status' => 1,
                'app_model_id' => 7,
                'attribute_set_id' => NULL,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
        ];
        DB::table('types')->insert($types_data);
    }
}
