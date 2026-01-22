<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $finance = Role::firstOrCreate(['name' => 'finance']);
        $warehouse = Role::firstOrCreate(['name' => 'warehouse']);

        User::where('role', 'admin')->chunk(100, function($users) use ($admin) {
            foreach($users as $user) {
                if (!$user->hasRole('admin')) {
                    $user->assignRole($admin);
                }
            }
        });
    }
}
