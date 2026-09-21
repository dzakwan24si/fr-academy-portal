<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            'manage_articles',
            'manage_users',
            'manage_tv_display',
            'manage_events',
            'manage_roles',
            'access_administrasi',
            'view_cost',
            'edit_cost',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Roles and Assign Default Permissions
        
        // Superadmin bypasses everything, but we create it anyway
        Role::firstOrCreate(['name' => 'Superadmin']);
        
        // Admin E-Information gets specific default permissions
        $adminInfoRole = Role::firstOrCreate(['name' => 'Admin E-Information']);
        $adminInfoRole->syncPermissions(['manage_articles', 'manage_tv_display', 'manage_events']);

        // Administrasi role gets full access to Administrasi & Cost
        $administrasiRole = Role::firstOrCreate(['name' => 'Administrasi']);
        $administrasiRole->syncPermissions(['access_administrasi', 'view_cost', 'edit_cost']);

        // Staff role gets access to Administrasi and read-only access to Cost
        $staffRole = Role::firstOrCreate(['name' => 'Staff']);
        $staffRole->syncPermissions(['access_administrasi', 'view_cost']);

        // Top Management role gets full access to Administrasi & Cost
        $topMgmtRole = Role::firstOrCreate(['name' => 'Top Management']);
        $topMgmtRole->syncPermissions(['access_administrasi', 'view_cost', 'edit_cost']);

        // Other generic roles
        $otherRoles = [
            'Koordinator Mess',
            'Kepala TU',
            'Atasan',
            'Trainer',
        ];

        foreach ($otherRoles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
