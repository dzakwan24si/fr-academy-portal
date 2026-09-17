<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function authenticateUser(): \App\Models\User
    {
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Superadmin', 'guard_name' => 'web']);
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin.test@domain.com'],
            ['name' => 'Test Superadmin', 'nik' => 'SUPERADMIN001', 'password' => bcrypt('password')]
        );
        if (!$user->hasRole('Superadmin')) {
            $user->assignRole($role);
        }
        $this->actingAs($user);
        return $user;
    }
}
