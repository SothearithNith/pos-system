<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class MasterAdminSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $menus = Menu::whereNotNull('permission_name')->get(); 
        foreach ($menus as $menu) {
            Permission::firstOrCreate([
                'name' => $menu->permission_name,
                'guard_name' => 'web',
            ]);
        }

        // Create Master Admin role
        $masterAdminRole = Role::firstOrCreate([
            'name' => 'Master Admin',
            'guard_name' => 'web',
        ]);

        // Assign all permissions to Master Admin
        $masterAdminRole->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        // Create default Master Admin user
        $masterAdmin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Master Admin',
                'password' => Hash::make('0!'),
                'status' => true,
            ]
        );

        // Assign Master Admin role
        $masterAdmin->syncRoles([$masterAdminRole]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
