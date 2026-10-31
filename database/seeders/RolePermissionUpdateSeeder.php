<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionUpdateSeeder extends Seeder
{
    /**
     * Seeder untuk menambahkan 2 permission baru saja:
     * 1. manage_career (Manajemen Karir)
     * 2. access_learning_activity (Akses Modul Learning Activity)
     *
     * Hanya menambahkan ke role yang bersangkutan tanpa menimpa/mengubah permission lain.
     */
    public function run(): void
    {
        // 1. Reset cache Spatie permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Tambahkan Permission "manage_career"
        $careerPermission = Permission::firstOrCreate([
            'name' => 'manage_career',
            'guard_name' => 'web',
        ]);

        // Role yang berhak mengelola Karir (CMS)
        $careerRoles = ['Superadmin', 'Admin E-Information'];
        foreach ($careerRoles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role && ! $role->hasPermissionTo($careerPermission)) {
                $role->givePermissionTo($careerPermission);
            }
        }

        // 3. Tambahkan Permission "access_learning_activity"
        $learningPermission = Permission::firstOrCreate([
            'name' => 'access_learning_activity',
            'guard_name' => 'web',
        ]);

        // Role yang berhak mengakses Modul Learning Activity / Diklat
        $learningRoles = ['Superadmin', 'Trainer', 'Administrasi', 'Staff', 'Koordinator Mess'];
        foreach ($learningRoles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role && ! $role->hasPermissionTo($learningPermission)) {
                $role->givePermissionTo($learningPermission);
            }
        }

        // 4. Refresh cache kembali
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
