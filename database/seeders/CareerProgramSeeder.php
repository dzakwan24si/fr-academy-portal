<?php

namespace Database\Seeders;

use App\Models\CareerProgram;
use Illuminate\Database\Seeder;

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
                'description' => 'Program pendidikan dan pelatihan intensif terakreditasi untuk mempersiapkan calon pemimpin operasional lapangan (Field Assistant / Asisten Kebun) yang berintegritas, berjiwa kepemimpinan kuat, dan menguasai teknik budidaya agronomi kelapa sawit berkelanjutan.',
                'qualifications' => '<ul><li>Pendidikan minimal S1/D4 jurusan Pertanian, Agroteknologi, Agronomi, Kehutanan, atau Biologi.</li><li>IPK minimal 2.75 dari skala 4.00.</li><li>Fresh graduate atau memiliki pengalaman kerja relevan di bidang perkebunan maksimal 2 tahun.</li><li>Memiliki jiwa kepemimpinan, komunikasi yang efektif, serta ketahanan fisik dan mental yang prima.</li><li>Bersedia menjalani ikatan dinas dan siap ditempatkan di seluruh unit operasional perkebunan First Resources Group (Riau, Kalimantan Barat, Kalimantan Timur).</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/FR-Career-FAT',
            ],
            [
                'name' => 'Mill Assistant Training (MAT)',
                'code' => 'MAT',
                'description' => 'Program jalur percepatan karir bagi calon Asisten Pabrik Kelapa Sawit (Mill Assistant) untuk menguasai manajemen operasional stasiun pengolahan TBS, boiler, turbin, efisiensi ekstraksi, mutu CPO & Kernel, serta pemeliharaan preventif mesin pabrik.',
                'qualifications' => '<ul><li>Pendidikan minimal S1/D4 jurusan Teknik Mesin, Teknik Elektro, Teknik Kimia, atau Teknik Industri.</li><li>IPK minimal 2.75 dari skala 4.00.</li><li>Memahami dasar-dasar mekanikal, elektrikal, instrumentasi, dan proses manufaktur/pengolahan pabrik.</li><li>Disiplin, analitis, mampu bekerja dalam sistem shift operasional, dan mengutamakan keselamatan kerja (K3).</li><li>Bersedia menjalani pelatihan dan siap ditempatkan di seluruh unit Pabrik Kelapa Sawit (PKS) First Resources Group.</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/FR-Career-MAT',
            ],
            [
                'name' => 'Traction Assistant Training (TAT)',
                'code' => 'TAT',
                'description' => 'Program pelatihan spesialis manajemen alat berat dan armada logistik perkebunan untuk mencetak calon Kepala Traksi / Workshop Assistant yang handal dalam mengoptimalkan utilisasi unit, perawatan armada, dan manajemen bengkel sentral.',
                'qualifications' => '<ul><li>Pendidikan minimal S1/D4/D3 jurusan Teknik Mesin, Teknik Otomotif, atau Alat Berat.</li><li>IPK minimal 2.75 dari skala 4.00.</li><li>Memiliki pengetahuan tentang sistem hidrolik, engine diesel, perawatan traktor, dump truck, dan alat berat (excavator, grader, dsb.).</li><li>Mampu menganalisis downtime unit, ketersediaan suku cadang, dan perbaikan mekanik.</li><li>Bersedia menjalani ikatan dinas dan siap ditempatkan di unit workshop/traksi kebun First Resources Group.</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/FR-Career-TAT',
            ],
            [
                'name' => 'Administration Assistant (AAT)',
                'code' => 'AAT',
                'description' => 'Program akselerasi karir profesional tata kelola administrasi kebun dan pabrik (KTU / Kepala Tata Usaha) yang bertanggung jawab atas akuntansi biaya operasional, manajemen kas, pergudangan persediaan, payroll, dan kepatuhan legalitas ketenagakerjaan.',
                'qualifications' => '<ul><li>Pendidikan minimal S1/D4 jurusan Akuntansi, Manajemen Keuangan, atau Ekonomi.</li><li>IPK minimal 2.75 dari skala 4.00.</li><li>Mahir mengoperasikan Microsoft Excel tingkat lanjutan (VLOOKUP, Pivot, Macro) dan memahami siklus akuntansi/ERP.</li><li>Teliti, memiliki integritas tinggi, ketelitian numerik yang tajam, dan pemahaman regulasi perpajakan/ketenagakerjaan dasar.</li><li>Bersedia ditempatkan di kantor administrasi kebun/pabrik kelapa sawit First Resources Group.</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/FR-Career-AAT',
            ],
            [
                'name' => 'Data Management Training (DMT)',
                'code' => 'DMT',
                'description' => 'Program pelatihan pengembangan talenta digital perkebunan modern untuk mengelola infrastruktur data operasional, validasi sistem GIS/pemetaan, integrasi aplikasi ERP lapangan, serta penyusunan dashboard analitik bisnis bagi manajemen.',
                'qualifications' => '<ul><li>Pendidikan minimal S1/D4 jurusan Sistem Informasi, Teknik Informatika, Ilmu Komputer, atau Statistika/Matematika.</li><li>IPK minimal 2.75 dari skala 4.00.</li><li>Menguasai pengolahan basis data (SQL), visualisasi data (PowerBI/Tableau/Excel lanjutan), dan dasar integrasi sistem digital.</li><li>Memiliki ketelitian tinggi dalam pembersihan dan validasi konsistensi data agrikultur.</li><li>Bersedia ditugaskan di unit operasional regional First Resources Group.</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/FR-Career-DMT',
            ],
            [
                'name' => 'Plantation Management Training (PMT)',
                'code' => 'PMT',
                'description' => 'Program pelatihan manajemen strategis perkebunan tingkat lanjut bagi calon pengelola estate (Askep / Estate Manager) untuk memperkuat kapabilitas perencanaan budget tahunan, kepemimpinan lintas fungsi, manajemen risiko operasional, dan kepatuhan standar keberlanjutan (ISPO/RSPO).',
                'qualifications' => '<ul><li>Pendidikan minimal S1 Pertanian, Kehutanan, Manajemen, atau Teknik Industri.</li><li>Memiliki pengalaman kerja relevan minimal 2–3 tahun di industri kelapa sawit atau fresh graduate berprestasi tinggi.</li><li>Memiliki rekam jejak kepemimpinan organisasi atau pengelolaan tim kerja di lapangan.</li><li>Memahami prinsip good agricultural practices (GAP) dan kepatuhan sertifikasi keberlanjutan.</li><li>Siap ditempatkan di remote area seluruh unit operasional First Resources Group.</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/FR-Career-PMT',
            ],
            [
                'name' => 'Management Associate',
                'code' => 'MA',
                'description' => 'Program kepemimpinan prestisius jalur cepat (fast-track leadership program) bagi talenta muda terbaik untuk dipersiapkan menduduki posisi pimpinan strategis masa depan melalui rotasi departemen, mentoring langsung oleh direksi eksekutif, dan penugasan proyek berdampak tinggi.',
                'qualifications' => '<ul><li>Pendidikan minimal S1/S2 semua jurusan dari universitas terkemuka dalam atau luar negeri.</li><li>IPK minimal 3.25 dari skala 4.00 (atau setara).</li><li>Fasih berkomunikasi lisan dan tertulis dalam Bahasa Indonesia dan Bahasa Inggris.</li><li>Memiliki kemampuan berpikir analitis, pemecahan masalah yang komprehensif, dan kecerdasan interpersonal yang tinggi.</li><li>Bersedia menjalani rotasi penugasan lintas wilayah dan divisi di seluruh unit First Resources Group.</li></ul>',
                'is_active' => true,
                'gform_url' => 'https://forms.gle/FR-Career-MA',
            ],
        ];

        foreach ($programs as $program) {
            CareerProgram::updateOrCreate(
                ['code' => $program['code']],
                $program
            );
        }
    }
}
