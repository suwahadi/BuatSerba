<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRBACSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'finance']);
        Role::firstOrCreate(['name' => 'warehouse']);

        // 1. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@buatserba.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin@buatserba.com'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );
        $admin->assignRole('admin');

        // 2. Finance User
        $finance = User::firstOrCreate(
            ['email' => 'finance@buatserba.com'],
            [
                'name' => 'Finance Staff',
                'password' => Hash::make('finance@buatserba.com'),
                'email_verified_at' => now(),
                'role' => 'finance',
            ]
        );
        $finance->assignRole('finance');

        // 3. Warehouse User
        $warehouse = User::firstOrCreate(
            ['email' => 'warehouse@buatserba.com'],
            [
                'name' => 'Warehouse Staff',
                'password' => Hash::make('warehouse@buatserba.com'),
                'email_verified_at' => now(),
                'role' => 'warehouse',
            ]
        );
        $warehouse->assignRole('warehouse');
    }
}
