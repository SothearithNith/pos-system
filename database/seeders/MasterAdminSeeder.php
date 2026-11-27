<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
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

        // One permission per menu item
        Menu::whereNotNull('permission_name')
            ->pluck('permission_name')
            ->unique()
            ->each(fn($name) => Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]));

        // Master Admin role gets every permission
        $masterAdminRole = Role::firstOrCreate([
            'name' => 'Master Admin',
            'guard_name' => 'web',
        ]);

        $masterAdminRole->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        // Platform admin: client_id = NULL means not tied to any client
        $masterAdmin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'client_id' => null,
                'name_en'   => 'Master Admin',
                'name_kh'   => 'អ្នកគ្រប់គ្រងមេ',
                'password'  => Hash::make(env('MASTER_ADMIN_PASSWORD', '0')),
                'is_active' => true,
            ]
        );

        $masterAdmin->syncRoles([$masterAdminRole]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
