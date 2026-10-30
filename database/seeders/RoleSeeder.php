<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            // Dashboard
            'view_dashboard',
            'edit_dashboard',

            // Program
            'view_program',
            'edit_program',

            // Monitoring
            'view_monitoring',
            'edit_monitoring',

            // Talent
            'view_talent',
            'edit_talent',

            // Administrasi
            'view_administrasi',
            'edit_administrasi',
            'access_administrasi', // Legacy backward-compatibility

            // Sub-modul Cost di Administrasi
            'view_cost',
            'edit_cost',

            // Data Aset
            'view_data_aset',
            'edit_data_aset',

            // Manajemen Umum & Sistem
            'manage_articles',
            'manage_users',
            'manage_tv_display',
            'manage_events',
            'manage_roles',
            'access_learning_activity',
            'manage_career',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Roles and Assign Default Permissions

        // Superadmin bypasses everything, but we create it anyway
        Role::firstOrCreate(['name' => 'Superadmin']);

        // Admin E-Information gets specific default permissions
        $adminInfoRole = Role::firstOrCreate(['name' => 'Admin E-Information']);
        $adminInfoRole->syncPermissions([
            'manage_articles',
            'manage_tv_display',
            'manage_events',
            'manage_career',
        ]);

        // Administrasi role gets full access to all operational modules & Cost
        $administrasiRole = Role::firstOrCreate(['name' => 'Administrasi']);
        $administrasiRole->syncPermissions([
            'view_dashboard', 'edit_dashboard',
            'view_program', 'edit_program',
            'view_monitoring', 'edit_monitoring',
            'view_talent', 'edit_talent',
            'view_administrasi', 'edit_administrasi', 'access_administrasi',
            'view_cost', 'edit_cost',
            'view_data_aset', 'edit_data_aset',
        ]);

        // Staff role gets access to Administrasi and read-only access to Cost
        $staffRole = Role::firstOrCreate(['name' => 'Staff']);
        $staffRole->syncPermissions([
            'view_dashboard',
            'view_program', 'edit_program',
            'view_monitoring', 'edit_monitoring',
            'view_talent', 'edit_talent',
            'view_administrasi', 'edit_administrasi', 'access_administrasi',
            'view_cost', // ONLY READ COST, NO EDIT COST
            'view_data_aset', 'edit_data_aset',
        ]);

        // Top Management role gets full access
        $topMgmtRole = Role::firstOrCreate(['name' => 'Top Management']);
        $topMgmtRole->syncPermissions([
            'view_dashboard', 'edit_dashboard',
            'view_program', 'edit_program',
            'view_monitoring', 'edit_monitoring',
            'view_talent', 'edit_talent',
            'view_administrasi', 'edit_administrasi', 'access_administrasi',
            'view_cost', 'edit_cost',
            'view_data_aset', 'edit_data_aset',
        ]);

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
