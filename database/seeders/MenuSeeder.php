<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Groups hold children. sort_order is generated from the position in this list.
        $tree = [
            [
                'slug' => 'dashboard',
                'en' => 'Dashboard',
                'kh' => 'ផ្ទាំងគ្រប់គ្រង',
                'icon' => 'bi bi-speedometer2',
                'route' => 'admin.dashboard',
                'perm' => 'dashboard.view',
            ],

            // ---------- Sales & cash register ----------
            [
                'slug' => 'sales-cash',
                'en' => 'Sales & Cash',
                'kh' => 'ការលក់ និងសាច់ប្រាក់',
                'icon' => 'bi bi-receipt',
                'perm' => 'sales-cash.view',
                'children' => [
                    ['slug' => 'sales', 'en' => 'Sales', 'kh' => 'ការលក់', 'icon' => 'bi bi-receipt-cutoff', 'route' => 'admin.sales', 'perm' => 'sales.view'],
                    ['slug' => 'shifts', 'en' => 'Shifts', 'kh' => 'វេន', 'icon' => 'bi bi-clock-history', 'route' => 'admin.shifts', 'perm' => 'shifts.view'],
                    ['slug' => 'cash-registers', 'en' => 'Cash Registers', 'kh' => 'ម៉ាស៊ីនគិតលុយ', 'icon' => 'bi bi-pc-display', 'route' => 'admin.cash-registers', 'perm' => 'cash-registers.view'],
                ],
            ],

            // ---------- Purchasing ----------
            [
                'slug' => 'purchasing',
                'en' => 'Purchasing',
                'kh' => 'ការទិញចូល',
                'icon' => 'bi bi-cart-plus',
                'perm' => 'purchasing.view',
                'children' => [
                    ['slug' => 'purchase-orders', 'en' => 'Purchase Orders', 'kh' => 'ការបញ្ជាទិញ', 'icon' => 'bi bi-bag-check', 'route' => 'admin.purchase-orders', 'perm' => 'purchase-orders.view'],
                    ['slug' => 'suppliers', 'en' => 'Suppliers', 'kh' => 'អ្នកផ្គត់ផ្គង់', 'icon' => 'bi bi-truck', 'route' => 'admin.suppliers', 'perm' => 'suppliers.view'],
                ],
            ],

            // ---------- Inventory ----------
            [
                'slug' => 'inventory',
                'en' => 'Inventory',
                'kh' => 'ស្តុកទំនិញ',
                'icon' => 'bi bi-boxes',
                'perm' => 'inventory.view',
                'children' => [
                    ['slug' => 'stocks', 'en' => 'Stock', 'kh' => 'ស្តុក', 'icon' => 'bi bi-box2', 'route' => 'admin.stocks', 'perm' => 'stocks.view'],
                    ['slug' => 'stock-movements', 'en' => 'Stock Movements', 'kh' => 'ចលនាស្តុក', 'icon' => 'bi bi-arrow-left-right', 'route' => 'admin.stock-movements', 'perm' => 'stock-movements.view'],
                    ['slug' => 'stock-checks', 'en' => 'Stock Checks', 'kh' => 'ការរាប់ស្តុក', 'icon' => 'bi bi-clipboard-check', 'route' => 'admin.stock-checks', 'perm' => 'stock-checks.view'],
                    ['slug' => 'stock-adjustments', 'en' => 'Stock Adjustments', 'kh' => 'ការកែតម្រូវស្តុក', 'icon' => 'bi bi-pencil-square', 'route' => 'admin.stock-adjustments', 'perm' => 'stock-adjustments.view'],
                    ['slug' => 'stock-transfers', 'en' => 'Stock Transfers', 'kh' => 'ការផ្ទេរស្តុក', 'icon' => 'bi bi-truck-flatbed', 'route' => 'admin.stock-transfers', 'perm' => 'stock-transfers.view'],
                ],
            ],

            // ---------- Catalog ----------
            [
                'slug' => 'catalog',
                'en' => 'Catalog',
                'kh' => 'កាតាឡុកផលិតផល',
                'icon' => 'bi bi-box-seam',
                'perm' => 'catalog.view',
                'children' => [
                    ['slug' => 'products', 'en' => 'Products', 'kh' => 'ផលិតផល', 'icon' => 'bi bi-box-seam', 'route' => 'admin.products', 'perm' => 'products.view'],
                    ['slug' => 'brands', 'en' => 'Brands', 'kh' => 'ម៉ាកផលិតផល', 'icon' => 'bi bi-tags', 'route' => 'admin.brands', 'perm' => 'brands.view'],
                    ['slug' => 'categories', 'en' => 'Categories', 'kh' => 'ប្រភេទផលិតផល', 'icon' => 'bi bi-grid', 'route' => 'admin.categories', 'perm' => 'categories.view'],
                    ['slug' => 'colors', 'en' => 'Colors', 'kh' => 'ពណ៌', 'icon' => 'bi bi-palette', 'route' => 'admin.colors', 'perm' => 'colors.view'],
                    ['slug' => 'sizes', 'en' => 'Sizes', 'kh' => 'ទំហំ', 'icon' => 'bi bi-rulers', 'route' => 'admin.sizes', 'perm' => 'sizes.view'],
                ],
            ],

            // ---------- Customers & discounts ----------
            [
                'slug' => 'customer-management',
                'en' => 'Customers & Discounts',
                'kh' => 'អតិថិជន និងការបញ្ចុះតម្លៃ',
                'icon' => 'bi bi-people',
                'perm' => 'customer-management.view',
                'children' => [
                    ['slug' => 'customers', 'en' => 'Customers', 'kh' => 'អតិថិជន', 'icon' => 'bi bi-person-lines-fill', 'route' => 'admin.customers', 'perm' => 'customers.view'],
                    ['slug' => 'customer-groups', 'en' => 'Customer Groups', 'kh' => 'ក្រុមអតិថិជន', 'icon' => 'bi bi-award', 'route' => 'admin.customer-groups', 'perm' => 'customer-groups.view'],
                    ['slug' => 'discounts', 'en' => 'Discounts', 'kh' => 'ការបញ្ចុះតម្លៃ', 'icon' => 'bi bi-percent', 'route' => 'admin.discounts', 'perm' => 'discounts.view'],
                ],
            ],

            // ---------- Settings ----------
            [
                'slug' => 'settings',
                'en' => 'Settings',
                'kh' => 'ការកំណត់',
                'icon' => 'bi bi-gear',
                'perm' => 'settings.view',
                'children' => [
                    ['slug' => 'branches', 'en' => 'Branches', 'kh' => 'សាខា', 'icon' => 'bi bi-building', 'route' => 'admin.settings.branches', 'perm' => 'branches.view'],
                    ['slug' => 'stock-locations', 'en' => 'Stock Locations', 'kh' => 'ទីតាំងស្តុក', 'icon' => 'bi bi-geo-alt', 'route' => 'admin.settings.stock-locations', 'perm' => 'stock-locations.view'],
                    ['slug' => 'payment-methods', 'en' => 'Payment Methods', 'kh' => 'វិធីបង់ប្រាក់', 'icon' => 'bi bi-credit-card', 'route' => 'admin.settings.payment-methods', 'perm' => 'payment-methods.view'],
                    ['slug' => 'users', 'en' => 'Users', 'kh' => 'អ្នកប្រើប្រាស់', 'icon' => 'bi bi-person-gear', 'route' => 'admin.settings.users', 'perm' => 'users.view'],
                    ['slug' => 'roles', 'en' => 'Roles & Permissions', 'kh' => 'តួនាទី និងសិទ្ធិ', 'icon' => 'bi bi-shield-lock', 'route' => 'admin.settings.roles', 'perm' => 'roles.view'],
                    ['slug' => 'system-configuration', 'en' => 'System Configuration', 'kh' => 'ការកំណត់ប្រព័ន្ធ', 'icon' => 'bi bi-sliders', 'route' => 'admin.settings.configuration', 'perm' => 'system-configuration.view'],
                ],
            ],

            // ---------- Platform admin only (give this permission to Master Admin only) ----------
            [
                'slug' => 'clients',
                'en' => 'Clients',
                'kh' => 'ម្ចាស់ហាង',
                'icon' => 'bi bi-shop',
                'route' => 'admin.clients',
                'perm' => 'clients.view',
            ],
        ];

        $order = 0;
        $slugs = [];

        foreach ($tree as $item) {
            $parent = $this->save($item, null, ++$order, $slugs);

            foreach ($item['children'] ?? [] as $child) {
                $this->save($child, $parent->id, ++$order, $slugs);
            }
        }

        // menus removed from this list are hidden instead of deleted
        Menu::whereNotIn('slug', $slugs)->update(['is_active' => false]);
    }

    private function save(array $item, ?int $parentId, int $order, array &$slugs): Menu
    {
        $slugs[] = $item['slug'];

        return Menu::updateOrCreate(
            ['slug' => $item['slug']],
            [
                'parent_id' => $parentId,
                'name_en' => $item['en'],
                'name_kh' => $item['kh'],
                'icon' => $item['icon'] ?? null,
                'route_name' => $item['route'] ?? null,
                'permission_name' => $item['perm'] ?? null,
                'sort_order' => $order,
                'is_active' => true,
            ]
        );
    }
}
