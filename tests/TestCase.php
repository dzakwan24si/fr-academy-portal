<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    protected function authenticateUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'Superadmin', 'guard_name' => 'web']);
        $user = User::firstOrCreate(
            ['email' => 'admin.test@domain.com'],
            ['name' => 'Test Superadmin', 'nik' => 'SUPERADMIN001', 'password' => bcrypt('password')]
        );
        if (! $user->hasRole('Superadmin')) {
            $user->assignRole($role);
        }
        $this->actingAs($user);

        return $user;
    }
}
