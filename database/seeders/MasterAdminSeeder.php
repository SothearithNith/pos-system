<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/** Run last: after MenuSeeder and RoleSeeder. */
class MasterAdminSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // permissions that come from menus (RoleSeeder already created the action permissions)
        Menu::whereNotNull('permission_name')
            ->pluck('permission_name')
            ->unique()
            ->each(fn($name) => Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]));

        // Platform role: not shared with clients
        $masterAdminRole = Role::firstOrCreate(
            ['client_id' => null, 'name' => 'Master Admin', 'guard_name' => 'web'],
            ['name_kh' => 'អ្នកគ្រប់គ្រងប្រព័ន្ធ', 'is_system' => true, 'is_platform' => true, 'is_active' => true]
        );

        $masterAdminRole->syncPermissions(Permission::where('guard_name', 'web')->get());

        // Platform admin user: client_id = NULL, not tied to any client
        $masterAdmin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'client_id' => null,
                'name_en'   => 'Master Admin',
                'name_kh'   => 'អ្នកគ្រប់គ្រងមេ',
                'password'  => Hash::make(env('MASTER_ADMIN_PASSWORD', 'ChangeMe!2026')),
                'is_active' => true,
            ]
        );

        $masterAdmin->syncRoles([$masterAdminRole]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
