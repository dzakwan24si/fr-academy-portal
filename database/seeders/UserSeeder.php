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
        
        // ----------------------------------------------------
        // 1. Akun Role Administrasi (Full View & Edit Access)
        // ----------------------------------------------------
        $adminUsers = [
            ['email' => 'admin@fr-academy.id', 'name' => 'Staff Administrasi Utama'],
            ['email' => 'admin.ops@fr-academy.id', 'name' => 'Admin Operasional'],
            ['email' => 'admin.program@fr-academy.id', 'name' => 'Admin Program & Diklat'],
        ];

        foreach ($adminUsers as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => Hash::make('password123')]
            );
            $user->syncRoles(['Administrasi']);
        }

        // ----------------------------------------------------
        // 2. Akun Role Staff (General Edit, Read-Only Cost)
        // ----------------------------------------------------
        $staffUsers = [
            ['email' => 'staff@fr-academy.id', 'name' => 'Staff Regular Utama'],
            ['email' => 'staff.lapangan@fr-academy.id', 'name' => 'Staff Lapangan'],
            ['email' => 'staff.diklat@fr-academy.id', 'name' => 'Staff Admin Diklat'],
        ];

        foreach ($staffUsers as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => Hash::make('password123')]
            );
            $user->syncRoles(['Staff']);
        }

        // ----------------------------------------------------
        // 3. Akun Role Top Management (Redirects to /Top_Management)
        // ----------------------------------------------------
        $topMgmtUsers = [
            ['email' => 'topmanagement@fr-academy.id', 'name' => 'Top Executive Management'],
            ['email' => 'direktur@fr-academy.id', 'name' => 'Direktur Akademi FR'],
            ['email' => 'vp.operation@fr-academy.id', 'name' => 'VP Operation FR'],
        ];

        foreach ($topMgmtUsers as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => Hash::make('password123')]
            );
            $user->syncRoles(['Top Management']);
        }
    }
}
