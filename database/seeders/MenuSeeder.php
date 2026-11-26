<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'name_kh' => 'ផ្ទាំងគ្រប់គ្រង',
                'name_en' => 'Dashboard',
                'slug' => 'dashboard',
                'icon' => 'bi bi-speedometer2',
                'route_name' => 'admin.dashboard',
                'permission_name' => 'dashboard.view',
                'parent_id' => null,
                'sort_order' => 1,
            ],
            [
                'name_kh' => 'វេន',
                'name_en' => 'Shifts',
                'slug' => 'shifts',
                'icon' => 'bi bi-clock-history',
                'route_name' => 'admin.shifts',
                'permission_name' => 'shifts.view',
                'parent_id' => null,
                'sort_order' => 2,
            ],
            [
                'name_kh' => 'ស្តុកទំនិញ',
                'name_en' => 'Inventory',
                'slug' => 'inventory',
                'icon' => 'bi bi-boxes',
                'route_name' => 'admin.inventory',
                'permission_name' => 'inventory.view',
                'parent_id' => null,
                'sort_order' => 3,
            ],
            [
                'name_kh' => 'ផលិតផល',
                'name_en' => 'Products',
                'slug' => 'products',
                'icon' => 'bi bi-box-seam',
                'route_name' => 'admin.products',
                'permission_name' => 'products.view',
                'parent_id' => 'inventory',
                'sort_order' => 4,
            ],
            [
                'name_kh' => 'ម៉ាកផលិតផល',
                'name_en' => 'Brands',
                'slug' => 'brands',
                'icon' => 'bi bi-tags',
                'route_name' => 'admin.brands',
                'permission_name' => 'brands.view',
                'parent_id' => 'inventory',
                'sort_order' => 5,
            ],
            [
                'name_kh' => 'ប្រភេទផលិតផល',
                'name_en' => 'Categories',
                'slug' => 'categories',
                'icon' => 'bi bi-grid',
                'route_name' => 'admin.categories',
                'permission_name' => 'categories.view',
                'parent_id' => 'inventory',
                'sort_order' => 6,
            ],
            [
                'name_kh' => 'ចលនាស្តុក',
                'name_en' => 'Stock Movements',
                'slug' => 'stock-movements',
                'icon' => 'bi bi-arrow-left-right',
                'route_name' => 'admin.stock',
                'permission_name' => 'stock.view',
                'parent_id' => 'inventory',
                'sort_order' => 7,
            ],
            [
                'name_kh' => 'ការបញ្ជាទិញ',
                'name_en' => 'Purchase Orders',
                'slug' => 'purchase-orders',
                'icon' => 'bi bi-cart-plus',
                'route_name' => 'admin.purchase-orders',
                'permission_name' => 'purchase-orders.view',
                'parent_id' => 'inventory',
                'sort_order' => 8,
            ],
            [
                'name_kh' => 'ការលក់',
                'name_en' => 'Sales',
                'slug' => 'sales',
                'icon' => 'bi bi-receipt',
                'route_name' => 'admin.sales',
                'permission_name' => 'sales.view',
                'parent_id' => 'inventory',
                'sort_order' => 9,
            ],
            [
                'name_kh' => 'អ្នកផ្គត់ផ្គង់',
                'name_en' => 'Suppliers',
                'slug' => 'suppliers',
                'icon' => 'bi bi-truck',
                'route_name' => 'admin.suppliers',
                'permission_name' => 'suppliers.view',
                'parent_id' => 'inventory',
                'sort_order' => 10,
            ],
            [
                'name_kh' => 'អតិថិជន',
                'name_en' => 'Customers',
                'slug' => 'customers',
                'icon' => 'bi bi-people',
                'route_name' => 'admin.customers',
                'permission_name' => 'customers.view',
                'parent_id' => 'inventory',
                'sort_order' => 11,
            ],
            [
                'name_kh' => 'ការកំណត់',
                'name_en' => 'Settings',
                'slug' => 'settings',
                'icon' => 'bi bi-gear',
                'route_name' => 'admin.settings',
                'permission_name' => 'settings.view',
                'parent_id' => null,
                'sort_order' => 12,
            ],
            [
                'name_kh' => 'សាខា និងឧបករណ៍',
                'name_en' => 'Branches & Devices',
                'slug' => 'branches-devices',
                'icon' => 'bi bi-building',
                'route_name' => 'admin.settings.branches',
                'permission_name' => 'branches.view',
                'parent_id' => 'settings',
                'sort_order' => 13,
            ],
            [
                'name_kh' => 'ការកំណត់ប្រព័ន្ធ',
                'name_en' => 'System Configuration',
                'slug' => 'system-configuration',
                'icon' => 'bi bi-sliders',
                'route_name' => 'admin.settings.configuration',
                'permission_name' => 'system-configuration.view',
                'parent_id' => 'settings',
                'sort_order' => 14,
            ],
        ];

        foreach ($menus as $menu) {
            $parentSlug = $menu['parent_id'];
            unset($menu['parent_id']);

            $parent = $parentSlug
                ? Menu::where('slug', $parentSlug)->firstOrFail()
                : null;

            Menu::updateOrCreate(
                ['slug' => $menu['slug']],
                array_merge($menu, [
                    'parent_id' => $parent?->id,
                    'status' => true,
                ])
            );
        }
    }
}
