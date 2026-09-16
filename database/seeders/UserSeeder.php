<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat akun Superadmin Yogie Hizkia
        $superadmin = User::firstOrCreate(
            ['email' => 'yogie.hizkia@fr-academy.id'], // Ganti dengan email asli jika ada
            [
                'name' => 'Yogie Hizkia',
                'password' => Hash::make('password123'), // Default password
            ]
        );

        // Assign role Superadmin
        $superadmin->assignRole('Superadmin');

        // Buat akun Admin Informasi
        $adminInfo = User::firstOrCreate(
            ['email' => 'admin.informasi@fr-academy.id'],
            [
                'name' => 'Admin E-Information',
                'password' => Hash::make('password123'),
            ]
        );
        $adminInfo->assignRole('Admin E-Information');
        
        // Buat akun Staff Administrasi
        $admin = User::firstOrCreate(
            ['email' => 'admin@fr-academy.id'],
            [
                'name' => 'Staff Administrasi',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->assignRole('Administrasi');
    }
}
