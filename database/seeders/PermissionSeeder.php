<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for Return Request Resource
        Permission::firstOrCreate(['name' => 'resource.return_requests.view_any', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'resource.return_requests.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'resource.return_requests.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'resource.return_requests.update', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'resource.return_requests.delete', 'guard_name' => 'web']);
    }
}
