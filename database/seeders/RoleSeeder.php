<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /** module => actions. Permission name = "module.action". */
    public const MODULES = [
        // menu groups
        'dashboard' => ['view'],
        'sales-cash' => ['view'],
        'purchasing' => ['view'],
        'inventory' => ['view'],
        'catalog' => ['view'],
        'customer-management' => ['view'],
        'settings' => ['view'],

        // sales & cash register
        'sales' => ['view', 'create', 'void'],
        'shifts' => ['view', 'create', 'update'],      // create = open shift, update = close shift
        'cash-registers' => ['view', 'create', 'update', 'delete'],

        // purchasing
        'purchase-orders' => ['view', 'create', 'update', 'delete', 'receive'],
        'suppliers' => ['view', 'create', 'update', 'delete'],

        // inventory
        'stocks' => ['view'],
        'stock-movements' => ['view'],
        'stock-checks' => ['view', 'create', 'update', 'approve'],
        'stock-adjustments' => ['view', 'create', 'update', 'approve'],
        'stock-transfers' => ['view', 'create', 'update', 'receive'],

        // catalog
        'products' => ['view', 'create', 'update', 'delete'],
        'brands' => ['view', 'create', 'update', 'delete'],
        'categories'  => ['view', 'create', 'update', 'delete'],
        'colors'  => ['view', 'manage'],   // shared lists, managed by the platform
        'sizes' => ['view', 'manage'],

        // customers
        'customers' => ['view', 'create', 'update', 'delete'],
        'customer-groups'  => ['view', 'create', 'update', 'delete'],
        'discounts' => ['view', 'create', 'update', 'delete'],

        // settings
        'branches' => ['view', 'create', 'update', 'delete'],
        'stock-locations' => ['view', 'create', 'update', 'delete'],
        'payment-methods' => ['view', 'create', 'update', 'delete'],
        'users'  => ['view', 'create', 'update', 'delete'],
        'roles' => ['view', 'create', 'update', 'delete'],
        'system-configuration' => ['view', 'update'],

        // platform only
        'clients' => ['view', 'create', 'update', 'delete'],
    ];

    /** Never given to client roles. */
    public const PLATFORM_ONLY = ['clients.*', 'colors.manage', 'sizes.manage'];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1) make sure every permission exists
        $all = [];
        foreach (self::MODULES as $module => $actions) {
            foreach ($actions as $action) {
                $all[] = "{$module}.{$action}";
                Permission::firstOrCreate(['name' => "{$module}.{$action}", 'guard_name' => 'web']);
            }
        }

        // 2) default roles
        $defaults = [
            'Admin' => [
                'name_kh' => 'អ្នកគ្រប់គ្រង',
                'allow'   => ['*'],
                'except'  => self::PLATFORM_ONLY,
            ],
            'Accountant' => [
                'name_kh' => 'គណនេយ្យករ',
                'allow'   => [
                    'dashboard.view',
                    'sales-cash.view',
                    'sales.view',
                    'shifts.view',
                    'cash-registers.view',
                    'purchasing.view',
                    'purchase-orders.view',
                    'purchase-orders.create',
                    'purchase-orders.update',
                    'suppliers.view',
                    'suppliers.create',
                    'suppliers.update',
                    'inventory.view',
                    'stocks.view',
                    'stock-movements.view',
                    'stock-checks.view',
                    'stock-checks.approve',
                    'stock-adjustments.view',
                    'stock-adjustments.approve',
                    'catalog.view',
                    'products.view',
                    'customer-management.view',
                    'customers.view',
                    'customer-groups.view',
                    'discounts.view',
                    'settings.view',
                    'payment-methods.view',
                ],
            ],
            'Stock' => [
                'name_kh' => 'អ្នកគ្រប់គ្រងស្តុក',
                'allow'   => [
                    'dashboard.view',
                    'purchasing.view',
                    'purchase-orders.view',
                    'purchase-orders.create',
                    'purchase-orders.update',
                    'purchase-orders.receive',
                    'suppliers.view',
                    'suppliers.create',
                    'suppliers.update',
                    'inventory.view',
                    'stocks.view',
                    'stock-movements.view',
                    'stock-checks.view',
                    'stock-checks.create',
                    'stock-checks.update',
                    'stock-adjustments.view',
                    'stock-adjustments.create',
                    'stock-adjustments.update',
                    'stock-transfers.*',
                    'catalog.view',
                    'products.view',
                    'products.create',
                    'products.update',
                    'brands.view',
                    'brands.create',
                    'brands.update',
                    'categories.view',
                    'categories.create',
                    'categories.update',
                    'colors.view',
                    'sizes.view',
                    'settings.view',
                    'stock-locations.view',
                ],
            ],
            'Cashier' => [
                'name_kh' => 'អ្នកគិតលុយ',
                'allow'   => [
                    'sales-cash.view',
                    'sales.view',
                    'sales.create',
                    'shifts.view',
                    'shifts.create',
                    'shifts.update',
                    'cash-registers.view',
                    'customer-management.view',
                    'customers.view',
                    'customers.create',
                    'catalog.view',
                    'products.view',
                    'inventory.view',
                    'stocks.view',
                ],
            ],
        ];

        foreach ($defaults as $name => $def) {
            $role = Role::firstOrCreate(
                ['client_id' => null, 'name' => $name, 'guard_name' => 'web'],
                ['is_system' => true, 'is_platform' => false, 'is_active' => true]
            );
            $role->update(['name_kh' => $def['name_kh'], 'is_system' => true]);

            $role->syncPermissions($this->expand($def['allow'], $def['except'] ?? [], $all));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /** Turn patterns like "stock-transfers.*" or "*" into permission names. */
    private function expand(array $allow, array $except, array $all): array
    {
        $match = fn(string $perm, array $patterns) => collect($patterns)
            ->contains(fn($p) => $p === '*' || $p === $perm
                || (str_ends_with($p, '.*') && str_starts_with($perm, substr($p, 0, -1))));

        return array_values(array_filter(
            $all,
            fn($perm) => $match($perm, $allow) && ! $match($perm, $except)
        ));
    }
}
