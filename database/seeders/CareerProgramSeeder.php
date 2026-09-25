<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CareerProgram;

class CareerProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Field Assistant Training (FAT)',
                'code' => 'FAT',
                'description' => 'Program pelatihan untuk menjadi asisten lapangan yang tangguh dan menguasai teknis agronomi perkebunan.',
                'qualifications' => '<ul><li>Pendidikan minimal S1 Pertanian/Kehutanan/Biologi</li><li>IPK minimal 2.75</li><li>Bersedia ditempatkan di seluruh wilayah operasional perusahaan</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/placeholder_fat',
            ],
            [
                'name' => 'Mill Assistant Training (MAT)',
                'code' => 'MAT',
                'description' => 'Program persiapan menjadi asisten pabrik kelapa sawit dengan kemampuan operasional handal.',
                'qualifications' => '<ul><li>Pendidikan minimal S1 Teknik Mesin/Teknik Elektro/Teknik Kimia</li><li>IPK minimal 2.75</li><li>Bersedia ditempatkan di seluruh wilayah operasional perusahaan</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/placeholder_mat',
            ],
            [
                'name' => 'Traction Assistant Training (TAT)',
                'code' => 'TAT',
                'description' => 'Mendidik calon pemimpin di bidang transportasi dan alat berat untuk mendukung operasional kebun.',
                'qualifications' => '<ul><li>Pendidikan minimal S1 Teknik Mesin</li><li>IPK minimal 2.75</li><li>Bersedia ditempatkan di seluruh wilayah operasional perusahaan</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/placeholder_tat',
            ],
            [
                'name' => 'Data Management Training (DMT)',
                'code' => 'DMT',
                'description' => 'Program pelatihan di bidang manajemen data dan sistem informasi operasional.',
                'qualifications' => '<ul><li>Pendidikan minimal S1 Sistem Informasi/Teknik Informatika</li><li>IPK minimal 2.75</li><li>Bersedia ditempatkan di seluruh wilayah operasional perusahaan</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/placeholder_dmt',
            ],
            [
                'name' => 'Administration Assistant (AAT)',
                'code' => 'AAT',
                'description' => 'Pelatihan khusus bidang tata usaha, keuangan, dan administrasi umum perkebunan serta pabrik.',
                'qualifications' => '<ul><li>Pendidikan minimal S1 Akuntansi/Manajemen/Ekonomi</li><li>IPK minimal 2.75</li><li>Bersedia ditempatkan di seluruh wilayah operasional perusahaan</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/placeholder_aat',
            ],
            [
                'name' => 'Plantation Management Training (PMT)',
                'code' => 'PMT',
                'description' => 'Program pelatihan manajemen perkebunan tingkat lanjut.',
                'qualifications' => '<ul><li>Pendidikan minimal S1 Pertanian/Manajemen</li><li>Pengalaman kerja relevan disukai</li><li>IPK minimal 2.75</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/placeholder_pmt',
            ],
            [
                'name' => 'Management Associate',
                'code' => 'MA',
                'description' => 'Program akselerasi karir untuk menjadi future leader di First Resources Group.',
                'qualifications' => '<ul><li>Pendidikan minimal S1 semua jurusan dari universitas terkemuka</li><li>IPK minimal 3.00</li><li>Aktif berorganisasi dan fasih berbahasa Inggris</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/placeholder_ma',
            ]
        ];

        foreach ($programs as $program) {
            CareerProgram::create($program);
        }
    }
}
