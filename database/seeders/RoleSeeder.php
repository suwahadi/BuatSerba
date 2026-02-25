<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->call(FilamentPermissionSeeder::class);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $finance = Role::firstOrCreate(['name' => 'finance', 'guard_name' => 'web']);
        $warehouse = Role::firstOrCreate(['name' => 'warehouse', 'guard_name' => 'web']);

        // Admin gets all permissions
        $admin->syncPermissions(Permission::query()->where('guard_name', 'web')->pluck('name')->all());

        // Finance permissions
        $financePermissions = [
            'resource.orders.view_any',
            'resource.orders.view',
            'resource.payments.view_any',
            'resource.payments.view',
            'resource.payment_confirmations.view_any',
            'resource.payment_confirmations.view',
            'resource.payment_confirmations.update',
            'resource.payment_confirmations.delete',
            'resource.internal_sales.view_any',
            'resource.internal_sales.view',
            'resource.internal_sales.create',
            'resource.internal_sales.update',
            'resource.internal_sales.delete',
            'resource.member_wallets.view_any',
            'resource.member_wallets.view',
            'resource.member_balance_ledgers.view_any',
            'resource.member_balance_ledgers.view',
            'page.orders.access',
            'page.point_of_sales.access',
            'page.pos_details.access',
            'page.expenses.access',
            'widget.latest_orders.access',
            'widget.dashboard_stats_overviews.access',
            'widget.reporting_best_selling_products.access',
            'widget.reporting_profit_charts.access',
            'widget.top_products_charts.access',
        ];
        $finance->syncPermissions($financePermissions);

        // Warehouse permissions
        $warehousePermissions = [
            'resource.master_products.view_any',
            'resource.master_products.view',
            'resource.master_products.create',
            'resource.master_products.update',
            'resource.master_products.delete',
            'resource.branches.view_any',
            'resource.branches.view',
            'resource.branches.update',
            'resource.stock_opnames.view_any',
            'resource.stock_opnames.view',
            'resource.stock_opnames.create',
            'resource.stock_opnames.update',
            'page.stocks_flows.access',
        ];
        $warehouse->syncPermissions($warehousePermissions);

        User::query()
            ->whereNotNull('role')
            ->chunk(100, function ($users) use ($admin, $finance, $warehouse) {
                foreach ($users as $user) {
                    $legacyRole = $user->role;
                    $role = match ($legacyRole) {
                        'admin' => $admin,
                        'finance' => $finance,
                        'warehouse' => $warehouse,
                        default => null,
                    };

                    if ($role && ! $user->hasRole($role->name)) {
                        $user->assignRole($role);
                    }
                }
            });
    }
}
