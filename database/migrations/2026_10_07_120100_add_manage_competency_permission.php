<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Menambahkan permission `manage_competency` secara idempoten.
 *
 * Sengaja lewat migration (bukan RoleSeeder): RoleSeeder memakai syncPermissions
 * dan akan menimpa pengaturan role yang sudah diubah lewat menu Roles.
 * Di sini hanya givePermissionTo, tidak pernah sync/menghapus permission lain.
 */
return new class extends Migration
{
    private const PERMISSION = 'manage_competency';

    private const ROLES = ['Administrasi', 'Superadmin'];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission = Permission::firstOrCreate([
            'name' => self::PERMISSION,
            'guard_name' => 'web',
        ]);

        foreach (self::ROLES as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

            if ($role && ! $role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Hanya menghapus permission ini; relasi role_has_permissions ikut terhapus oleh Spatie.
        Permission::where('name', self::PERMISSION)->where('guard_name', 'web')->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
