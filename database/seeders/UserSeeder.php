<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();
        $managerRole = \App\Models\Role::where('name', 'Manager')->first();
        $cashierRole = \App\Models\Role::where('name', 'Cashier')->first();

        if ($adminRole) {
            \App\Models\User::firstOrCreate(
                ['email' => 'admin@admin.com'],
                [
                    'name' => 'Admin User',
                    'password' => 'password',
                    'role_id' => $adminRole->id,
                ]
            );
        }

        if ($managerRole) {
            \App\Models\User::firstOrCreate(
                ['email' => 'manager@manager.com'],
                [
                    'name' => 'Manager User',
                    'password' => 'password',
                    'role_id' => $managerRole->id,
                ]
            );
        }

        if ($cashierRole) {
            \App\Models\User::firstOrCreate(
                ['email' => 'cashier@cashier.com'],
                [
                    'name' => 'Cashier User',
                    'password' => 'password',
                    'role_id' => $cashierRole->id,
                ]
            );
        }
    }
}
