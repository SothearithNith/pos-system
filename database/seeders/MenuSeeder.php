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
 
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['slug' => $menu['slug']],
                array_merge($menu, ['status' => true])
            );
        }
    }
}
