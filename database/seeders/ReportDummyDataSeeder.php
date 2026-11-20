<?php

namespace Database\Seeders;

use App\Models\Cost\BiayaRekruit;
use App\Models\Cost\CapexRab;
use App\Models\Cost\CapexRealisasi;
use App\Models\Cost\Cost;
use App\Models\Cost\Vra;
use App\Models\Inventaris\Barang;
use App\Models\Inventaris\Pembelian;
use App\Models\Inventaris\StockOpname;
use App\Models\Monitoring\Asisten;
use App\Models\Monitoring\MonitoringAngkatan;
use App\Models\Monitoring\MonitoringCulled;
use App\Models\Monitoring\MonitoringEmployee;
use App\Models\Monitoring\MonitoringTalent;
use App\Models\Program\ProgramNonReguler;
use App\Models\Program\ProgramReguler;
use App\Models\Program\RealisasiVsBatch;
use App\Models\Program\TrainingRecord;
use App\Models\Talent\ProfilTalent;
use App\Models\Talent\TalentPool;
use App\Services\TrainingMetricService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai pembersihan data operasional lama...');

        Schema::disableForeignKeyConstraints();

        TrainingRecord::truncate();
        RealisasiVsBatch::truncate();
        ProgramReguler::truncate();
        ProgramNonReguler::truncate();

        MonitoringEmployee::truncate();
        MonitoringCulled::truncate();
        MonitoringAngkatan::truncate();
        MonitoringTalent::truncate();
        Asisten::truncate();

        ProfilTalent::truncate();
        TalentPool::truncate();

        StockOpname::truncate();
        Pembelian::truncate();
        Barang::truncate();

        CapexRealisasi::truncate();
        CapexRab::truncate();
        Vra::truncate();
        BiayaRekruit::truncate();
        Cost::truncate();

        Schema::enableForeignKeyConstraints();

        $this->command->info('Data lama berhasil dibersihkan.');
        $this->command->info('Mengisikan data simulasi (Dummy Data) untuk laporan...');

        $year = 2026;

        // =========================================================================
        // 1. PROGRAM TRAINING & TRAINING RECORD
        // =========================================================================
        $prog1 = ProgramReguler::create([
            'Training_Program' => 'Plantation Staff Training Program (PSTP)',
            'Batch' => 'Batch 42',
            'tahun' => $year,
            'Kategori_Program' => 'Reguler',
            'Lokasi_training' => 'FRLC Riau',
            'Mulai_program' => '2026-02-01',
            'Selesai_program' => '2026-07-31',
            'target_peserta' => 30,
            'Region' => 'Riau',
            'staff' => 28,
            'lulus' => 26,
            'resign' => 2,
        ]);

        $prog2 = ProgramReguler::create([
            'Training_Program' => 'Mill Staff Training Program (MSTP)',
            'Batch' => 'Batch 28',
            'tahun' => $year,
            'Kategori_Program' => 'Reguler',
            'Lokasi_training' => 'FRLC Riau',
            'Mulai_program' => '2026-03-15',
            'Selesai_program' => '2026-09-15',
            'target_peserta' => 25,
            'Region' => 'Kalbar',
            'staff' => 24,
            'lulus' => 0,
            'resign' => 0,
        ]);

        $nonReg1 = ProgramNonReguler::create([
            'kategori_pelatihan' => 'Managerial and Leadership',
            'jenis_pelatihan' => 'Strategic Operational Leadership',
            'sub_kategori' => 'Softskill',
            'region' => 'Riau',
            'trainer' => 'Internal FRLC',
            'lokasi_training' => 'FR Learning Center Riau',
            'total_hari' => 3,
            'total_jam' => 24,
            'start_date' => '2026-04-10',
            'end_date' => '2026-04-12',
            'tahun' => $year,
        ]);

        // Training Records (Peserta)
        $pesertaPstp = [
            ['nama' => 'Ahmad Fajar Pratama', 'nik' => '2601001', 'gender' => 'L', 'univ' => 'Institut Pertanian Bogor'],
            ['nama' => 'Budi Santoso', 'nik' => '2601002', 'gender' => 'L', 'univ' => 'Universitas Gadjah Mada'],
            ['nama' => 'Citra Dewi Anggraini', 'nik' => '2601003', 'gender' => 'P', 'univ' => 'Universitas Riau'],
            ['nama' => 'Dedi Kurniawan', 'nik' => '2601004', 'gender' => 'L', 'univ' => 'Universitas Sumatera Utara'],
            ['nama' => 'Eko Prasetyo', 'nik' => '2601005', 'gender' => 'L', 'univ' => 'Universitas Andalas'],
            ['nama' => 'Farhan Maulana', 'nik' => '2601006', 'gender' => 'L', 'univ' => 'Universitas Brawijaya'],
            ['nama' => 'Gita Permatasari', 'nik' => '2601007', 'gender' => 'P', 'univ' => 'Universitas Lampung'],
            ['nama' => 'Hendra Saputra', 'nik' => '2601008', 'gender' => 'L', 'univ' => 'Universitas Jember'],
        ];

        foreach ($pesertaPstp as $p) {
            TrainingRecord::create([
                'program_type' => 'reguler',
                'program_id' => $prog1->Program_reguler_id,
                'participant_identifier' => $p['nik'],
                'participant_name' => $p['nama'],
                'gender' => $p['gender'],
                'company' => 'PT Panca Surya Garden',
                'department' => 'Agronomy',
                'region' => 'Riau',
                'status' => 'valid',
                'tahun' => $year,
                'bulan' => 5,
                'nama_pelatihan' => $prog1->Training_Program,
            ]);
        }

        // Realisasi vs Batch
        for ($m = 1; $m <= 12; $m++) {
            RealisasiVsBatch::create([
                'program_type' => 'reguler',
                'program_id' => $prog1->Program_reguler_id,
                'tahun' => $year,
                'bulan' => $m,
                'legacy_realized_batch' => $m <= 6 ? 1 : 0,
                'legacy_realized_participants' => $m <= 6 ? count($pesertaPstp) : 0,
            ]);
        }

        // =========================================================================
        // 2. MONITORING ALUMNI & DATABASE ASISTEN
        // =========================================================================
        $angkatan = MonitoringAngkatan::create([
            'angkatan' => 'PSTP Batch 41 (2025)',
            'jumlah_awal' => 28,
            'lulus' => 26,
            'resign_in_class' => 1,
            'resign_ojt' => 1,
            'tidak_lulus' => 0,
        ]);

        $asistens = [
            ['nama' => 'Rian Hidayat', 'nik' => '2501011', 'kebun' => 'Kebun Sei Rokan', 'region' => 'Riau', 'jabatan' => 'Asisten Afdeling', 'level' => 'Asisten', 'status' => 'Aktif'],
            ['nama' => 'Dimas Wicaksono', 'nik' => '2501012', 'kebun' => 'Kebun Ujung Tanjung', 'region' => 'Riau', 'jabatan' => 'Asisten Kepala (Askep)', 'level' => 'Askep', 'status' => 'Aktif'],
            ['nama' => 'Siti Nurhaliza', 'nik' => '2501013', 'kebun' => 'Kebun Pelalawan', 'region' => 'Riau', 'jabatan' => 'Asisten Afdeling', 'level' => 'Asisten', 'status' => 'Aktif'],
            ['nama' => 'Bayu Setiawan', 'nik' => '2501014', 'kebun' => 'Kebun Kalbar 1', 'region' => 'Kalbar', 'jabatan' => 'Estate Manager', 'level' => 'MGR', 'status' => 'Aktif'],
            ['nama' => 'Rizky Firmansyah', 'nik' => '2501015', 'kebun' => 'Kebun Sei Rokan', 'region' => 'Riau', 'jabatan' => 'Ex-Asisten', 'level' => 'Asisten', 'status' => 'Resign'],
            ['nama' => 'Ilham Wahyudi', 'nik' => '2501016', 'kebun' => 'Kebun Kampar', 'region' => 'Riau', 'jabatan' => 'Asisten Afdeling', 'level' => 'Asisten', 'status' => 'Aktif'],
            ['nama' => 'Nurul Hidayati', 'nik' => '2501017', 'kebun' => 'Kebun Mandau', 'region' => 'Riau', 'jabatan' => 'Asisten Agronomi', 'level' => 'Asisten', 'status' => 'Aktif'],
            ['nama' => 'Fajar Nugraha', 'nik' => '2501018', 'kebun' => 'Kebun Kalbar 2', 'region' => 'Kalbar', 'jabatan' => 'Asisten Afdeling', 'level' => 'Asisten', 'status' => 'Aktif'],
        ];

        foreach ($asistens as $idx => $a) {
            Asisten::create([
                'no' => $idx + 1,
                'nama' => $a['nama'],
                'nik' => $a['nik'],
                'jenis_kelamin' => 'L',
                'program' => 'Plantation Staff Training Program (PSTP)',
                'batch' => 'Batch 41',
                'tahun_angkatan' => 2025,
                'tahun_lulus' => 2025,
                'status_kelulusan' => 'LULUS',
                'asal_rekrut' => 'Pekanbaru',
                'universitas' => 'Universitas Riau',
                'jurusan' => 'Agroteknologi',
                'status_karyawan' => $a['status'] === 'Aktif' ? 'Staff Tetap' : 'Resigned',
                'status_resign' => $a['status'],
                'jabatan_terakhir' => $a['jabatan'],
                'level_jabatan' => $a['level'],
                'region_saat_ini' => $a['region'],
                'unit_kerja_saat_ini' => $a['kebun'],
                'pt_penempatan' => 'PT Panca Surya Garden',
                'keterangan' => $a['status'],
            ]);

            MonitoringEmployee::create([
                'nik' => $a['nik'],
                'nama' => $a['nama'],
                'angkatan_fr_academy' => 'PSTP Batch 41 (2025)',
                'lokasi_terakhir' => $a['kebun'],
                'region_terakhir' => $a['region'],
                'status' => $a['status'],
                'jabatan' => $a['jabatan'],
            ]);

            if ($a['status'] === 'Resign') {
                MonitoringCulled::create([
                    'id_angkatan' => $angkatan->id_angkatan,
                    'nama' => $a['nama'],
                    'alasan' => 'Mendapatkan tawaran karir lain di luar perkebunan.',
                    'kategori_status' => 'Resign Pasca OJT',
                    'jenis_culled' => 'Voluntary Resignation',
                ]);
            }
        }

        // =========================================================================
        // 3. TALENT (PROFIL TALENT & TALENT POOL)
        // =========================================================================
        $talents = [
            [
                'emp_id' => 'FR-TL-2021',
                'nama' => 'Ir. Muhammad Ilham',
                'jabatan' => 'Asisten Kepala Afdeling',
                'talent_to' => 'Estate Manager',
                'kategori' => 'High Potential (HiPo)',
                'region' => 'Riau',
                'mentor' => 'Ir. Bambang Sugiarto (Senior GM)',
                'status' => 'Ready in 1 Year',
            ],
            [
                'emp_id' => 'FR-TL-2022',
                'nama' => 'Agus Priyono, S.P.',
                'jabatan' => 'Kepala Tata Usaha (KTU)',
                'talent_to' => 'Regional Financial Controller',
                'kategori' => 'Star Talent',
                'region' => 'Riau',
                'mentor' => 'Hendro Wijaya (KTU Senior)',
                'status' => 'Ready Now',
            ],
            [
                'emp_id' => 'FR-TL-2023',
                'nama' => 'Doni Wahyudi, S.T.',
                'jabatan' => 'Asisten Mill Processing',
                'talent_to' => 'Mill Manager',
                'kategori' => 'Fast Tracker',
                'region' => 'Kalimantan Barat',
                'mentor' => 'Ir. Joko Susilo (Mill Head)',
                'status' => 'Ready in 2 Years',
            ],
        ];

        foreach ($talents as $t) {
            ProfilTalent::create([
                'employee_id' => $t['emp_id'],
                'nama' => $t['nama'],
                'jabatan' => $t['jabatan'],
                'talent_to' => $t['talent_to'],
                'talent' => $t['kategori'],
                'region' => $t['region'],
                'mentor' => $t['mentor'],
                'alumni' => 'PSTP Alumni',
            ]);

            TalentPool::create([
                'employee_id' => $t['emp_id'],
                'employee_name' => $t['nama'],
                'posisi_asal' => $t['jabatan'],
                'talent_to' => $t['talent_to'],
                'jenis_kategori' => $t['kategori'],
                'region' => $t['region'],
                'tahun' => $year,
                'status' => $t['status'],
                'mentor_lapangan' => $t['mentor'],
                'mentor_frlc' => 'Trainer Head FRLC',
            ]);
        }

        // Monitoring Talent (Dummy Records)
        $monitoringTalentData = [
            [
                'no' => 1,
                'emp_id' => '202601001',
                'name' => 'Budi Prasetyo, S.P.',
                'posisi_asal' => 'Asisten Kebun',
                'talent_to' => 'Askep (Asisten Kepala)',
                'unit' => 'Kebun Sei Rokan',
                'region' => 'Riau',
                'mentor_lap' => 'Ir. Bambang Sugiarto (Senior GM)',
                'mentor_frlc' => 'Trainer Head FRLC',
                'status' => 'Sudah Penugasan',
                'keterangan' => 'Plt. Askep Kebun Sei Rokan',
                'lama' => 'Periode 1 Januari - 30 April 2026',
                'baru' => 'Periode 1 Mei - 31 Agustus 2026',
                'lokasi_baru' => 'Tetap',
                'kategori' => 'TALENT ESTATE TAHUN 2026',
            ],
            [
                'no' => 2,
                'emp_id' => '202601002',
                'name' => 'Hendra Gunawan, S.T.',
                'posisi_asal' => 'Asisten Mill Processing',
                'talent_to' => 'Mill Manager',
                'unit' => 'PKS Ujung Tanjung',
                'region' => 'Riau',
                'mentor_lap' => 'Ir. Joko Susilo (Mill Head)',
                'mentor_frlc' => 'Senior Trainer Engineering',
                'status' => 'Sudah Penugasan',
                'keterangan' => 'Penugasan Assistant Mill Head',
                'lama' => 'Periode 1 Februari - 31 Mei 2026',
                'baru' => 'Periode 1 Juni - 30 September 2026',
                'lokasi_baru' => 'Tetap',
                'kategori' => 'TALENT MILL TAHUN 2026',
            ],
            [
                'no' => 3,
                'emp_id' => '202601003',
                'name' => 'Siti Rahmawati, S.E.',
                'posisi_asal' => 'Kepala Tata Usaha (KTU)',
                'talent_to' => 'Regional Financial Controller',
                'unit' => 'Kantor Wilayah Pelalawan',
                'region' => 'Riau',
                'mentor_lap' => 'Hendro Wijaya (KTU Senior)',
                'mentor_frlc' => 'Lead Trainer Finance',
                'status' => 'Dalam Evaluasi',
                'keterangan' => 'Evaluasi Kesiapan Sertifikasi Finansial',
                'lama' => 'Periode 1 Maret - 30 Juni 2026',
                'baru' => 'Periode 1 Juli - 31 Oktober 2026',
                'lokasi_baru' => 'Kantor Wilayah Riau',
                'kategori' => 'TALENT ESTATE TAHUN 2026',
            ],
            [
                'no' => 4,
                'emp_id' => '202601004',
                'name' => 'Eko Wahyudi, S.P.',
                'posisi_asal' => 'Asisten Agronomi & Riset',
                'talent_to' => 'Askep Agronomi',
                'unit' => 'Kebun Kalbar 1',
                'region' => 'Kalbar',
                'mentor_lap' => 'Agus Priyono (Head Agronomist)',
                'mentor_frlc' => 'Trainer Head FRLC',
                'status' => 'Sudah Penugasan',
                'keterangan' => 'Proyek Khusus Optimalisasi Pemupukan',
                'lama' => 'Periode 1 Januari - 30 April 2026',
                'baru' => 'Periode 1 Mei - 31 Agustus 2026',
                'lokasi_baru' => 'Tetap',
                'kategori' => 'TALENT ESTATE TAHUN 2026',
            ],
            [
                'no' => 5,
                'emp_id' => '202601005',
                'name' => 'Dedi Saputra, S.T.',
                'posisi_asal' => 'Asisten Maintenance Pabrik',
                'talent_to' => 'Askep Pabrik Kelapa Sawit',
                'unit' => 'PKS Rokan Hilir',
                'region' => 'Riau',
                'mentor_lap' => 'Haryanto (Manager Pabrik)',
                'mentor_frlc' => 'Senior Trainer Engineering',
                'status' => 'Siap Promosi',
                'keterangan' => 'Rekomendasi Promosi Semester 2',
                'lama' => 'Periode 1 Februari - 31 Mei 2026',
                'baru' => 'Periode 1 Juni - 30 September 2026',
                'lokasi_baru' => 'PKS Kalbar',
                'kategori' => 'TALENT MILL TAHUN 2026',
            ],
        ];

        foreach ($monitoringTalentData as $mt) {
            MonitoringTalent::create([
                'no' => $mt['no'],
                'employee_id' => $mt['emp_id'],
                'employee_name' => $mt['name'],
                'posisi_asal' => $mt['posisi_asal'],
                'talent_to' => $mt['talent_to'],
                'unit' => $mt['unit'],
                'region' => $mt['region'],
                'mentor_lapangan' => $mt['mentor_lap'],
                'mentor_frlc' => $mt['mentor_frlc'],
                'status' => $mt['status'],
                'keterangan' => $mt['keterangan'],
                'periode_penugasan_lama' => $mt['lama'],
                'periode_penugasan_baru' => $mt['baru'],
                'lokasi_penugasan_baru' => $mt['lokasi_baru'],
                'kategori' => $mt['kategori'],
            ]);
        }

        // =========================================================================
        // 4. INVENTARIS (BARANG, PEMBELIAN, STOCK OPNAME)
        // =========================================================================
        $barangData = [
            ['nama' => 'Laptop Lenovo ThinkPad L14 Gen 3', 'satuan' => 'Unit', 'lokasi' => 'Kantor', 'ket' => 'Laptop operasional staf training & admin'],
            ['nama' => 'Proyektor Epson EB-X500', 'satuan' => 'Unit', 'lokasi' => 'Kantor', 'ket' => 'Proyektor ruang kelas training A & B'],
            ['nama' => 'Kursi Kerja Ergonomis Mesh', 'satuan' => 'Buah', 'lokasi' => 'Kantor', 'ket' => 'Fasilitas kerja ruang administrasi'],
            ['nama' => 'Tempat Tidur Springbed Single 100x200', 'satuan' => 'Unit', 'lokasi' => 'Gudang', 'ket' => 'Cadangan fasilitas asrama trainee mess'],
            ['nama' => 'Genset Silent 50 KVA Cummins', 'satuan' => 'Unit', 'lokasi' => 'Gudang', 'ket' => 'Genset cadangan listrik komplek FRLC'],
        ];

        foreach ($barangData as $b) {
            $brg = Barang::create([
                'nama_asset' => $b['nama'],
                'satuan' => $b['satuan'],
                'jenis_lokasi' => $b['lokasi'],
                'keterangan' => $b['ket'],
            ]);

            Pembelian::create([
                'barang_id' => $brg->id,
                'tanggal_pembelian' => '2026-02-10',
                'jumlah' => 5,
                'lokasi_spesifik' => $b['lokasi'] === 'Kantor' ? 'Ruang Administrasi FRLC' : 'Gudang Logistik Utama',
                'keterangan' => 'Pengadaan aset awal tahun anggaran 2026',
            ]);

            StockOpname::create([
                'barang_id' => $brg->id,
                'tanggal_stock_opname' => '2026-06-01',
                'status' => 'Selesai',
                'lokasi_spesifik' => $b['lokasi'],
                'jumlah_baik' => 4,
                'jumlah_rusak' => 1,
                'grand_total' => 5,
                'catatan_sesi' => 'Audit fisik semester 1 tahun 2026 berjalan lancar.',
                'keterangan' => '1 unit dalam masa perbaikan teknisi.',
            ]);
        }

        // =========================================================================
        // 5. ADMINISTRASI & BIAYA (BIAYA REKRUIT, VRA, CAPEX, COST)
        // =========================================================================
        BiayaRekruit::create([
            'tahun' => $year,
            'tanggal' => '2026-01-20',
            'nama' => 'Psikotes & Wawancara PSTP Batch 42',
            'total_biaya' => 24500000,
            'jenis_rekruit' => 'Kampus Hiring IPB & UGM',
            'penanggung_jawab' => 'Tim Talent Acquisition HR',
        ]);

        BiayaRekruit::create([
            'tahun' => $year,
            'tanggal' => '2026-03-05',
            'nama' => 'Medical Check Up (MCU) Calon Trainee',
            'total_biaya' => 18200000,
            'jenis_rekruit' => 'Vendor RS Awal Bros',
            'penanggung_jawab' => 'Tim Rekrutmen FRLC',
        ]);

        // VRA (Operasional Kendaraan)
        $vehicles = [
            ['nopol' => 'BM 7012 FR', 'nama' => 'Bus Operasional Trainee (Hino)', 'km_awal' => 45000, 'km_akhir' => 47200, 'bbm' => 420, 'bbm_rp' => 5460000, 'servis' => 2500000, 'upah' => 3800000],
            ['nopol' => 'BM 8123 FR', 'nama' => 'Hilux Double Cabin Trainer 4x4', 'km_awal' => 62000, 'km_akhir' => 63800, 'bbm' => 280, 'bbm_rp' => 3640000, 'servis' => 1200000, 'upah' => 3500000],
            ['nopol' => 'BM 1024 FR', 'nama' => 'Fortuner Operasional Manajemen', 'km_awal' => 28000, 'km_akhir' => 29400, 'bbm' => 210, 'bbm_rp' => 3150000, 'servis' => 1800000, 'upah' => 3500000],
        ];

        foreach ($vehicles as $v) {
            $kmTempuh = $v['km_akhir'] - $v['km_awal'];
            $totalBulan = $v['bbm_rp'] + $v['servis'] + $v['upah'];
            Vra::create([
                'nomor_polisi' => $v['nopol'],
                'kendaraan' => $v['nama'],
                'tahun' => $year,
                'bulan' => 5,
                'upah' => $v['upah'],
                'BBM' => $v['bbm_rp'],
                'preventif_maintenance' => $v['servis'],
                'biaya_lainnya' => 500000,
                'total_biaya_bulan' => $totalBulan + 500000,
                'KM_awal' => $v['km_awal'],
                'KM_akhir' => $v['km_akhir'],
                'KM' => $kmTempuh,
                'BBM_liter' => $v['bbm'],
                'BBM_KM_liter' => round($kmTempuh / max(1, $v['bbm']), 2),
                'VRA' => round(($totalBulan + 500000) / max(1, $kmTempuh), 2),
                'budget_tahun' => 120000000,
            ]);
        }

        // Capex (RAB & Realisasi)
        $rab1 = CapexRab::create([
            'tahun' => $year,
            'lokasi' => 'kantor',
            'kel_barang_kapital' => 'Pengadaan Fasilitas Lab Komputer & Multimedia',
            'type' => 'Aset Teknologi Informasi',
            'satuan' => 'Paket',
            'jumlah_unit' => 1,
            'harga_satuan' => 75000000,
            'total_rp' => 75000000,
        ]);

        CapexRealisasi::create([
            'rab_id' => $rab1->rab_id,
            'tahun' => $year,
            'lokasi' => 'kantor',
            'kel_barang_kapital' => 'Pengadaan Fasilitas Lab Komputer & Multimedia',
            'tanggal' => '2026-03-25',
            'kredit' => 72500000,
            'total_rp' => 72500000,
            'nama_barang' => 'Realisasi pembelian 15 PC dan switch jaringan FRLC',
        ]);

        $rab2 = CapexRab::create([
            'tahun' => $year,
            'lokasi' => 'mess',
            'kel_barang_kapital' => 'Renovasi Sanitasi & Air Bersih Asrama Trainee',
            'type' => 'Sarana & Prasarana',
            'satuan' => 'Proyek',
            'jumlah_unit' => 1,
            'harga_satuan' => 45000000,
            'total_rp' => 45000000,
        ]);

        CapexRealisasi::create([
            'rab_id' => $rab2->rab_id,
            'tahun' => $year,
            'lokasi' => 'mess',
            'kel_barang_kapital' => 'Renovasi Sanitasi & Air Bersih Asrama Trainee',
            'tanggal' => '2026-05-18',
            'kredit' => 4400000,
            'total_rp' => 44000000,
            'nama_barang' => 'Penyelesaian instalasi filter air dan tandon asrama',
        ]);

        // Biaya Operasional Umum (Cost)
        $costs = [
            ['gl' => '850.91.01001', 'jenis' => 'BIAYA UMUM KANTOR', 'name' => 'Beban Alat Tulis Kantor (ATK) & Fotocopy', 'budget' => 3500000, 'realisasi' => 3150000, 'bulan' => 5],
            ['gl' => '850.91.01002', 'jenis' => 'BIAYA KONSUMSI', 'name' => 'Konsumsi Pelatihan Trainee & Kelas FRLC', 'budget' => 18000000, 'realisasi' => 17450000, 'bulan' => 5],
            ['gl' => '850.91.01003', 'jenis' => 'BIAYA UTILITAS', 'name' => 'Beban Listrik PLN, Air & Internet Fiber', 'budget' => 6500000, 'realisasi' => 6200000, 'bulan' => 5],
            ['gl' => '850.91.02001', 'jenis' => 'BIAYA PERJALANAN DINAS', 'name' => 'Transportasi & Tiket Dinas Trainer Luar', 'budget' => 8000000, 'realisasi' => 7800000, 'bulan' => 5],
        ];

        foreach ($costs as $c) {
            Cost::create([
                'GL_ACCOUNT' => $c['gl'],
                'jenis_cost' => $c['jenis'],
                'account_name' => $c['name'],
                'tahun' => $year,
                'bulan' => $c['bulan'],
                'kategori' => 'Operasional Rutin',
                'budget' => $c['budget'],
                'realisasi' => $c['realisasi'],
                'budget_tahunan' => $c['budget'] * 12,
                'tanggal' => '2026-05-28',
                'no_bukti' => 'BKK-2026-05/' . rand(100, 999),
            ]);
        }

        Cache::flush();
        TrainingMetricService::clearAllDashboardCaches();

        $this->command->info('SUKSES! Seluruh data simulasi (Dummy Data) untuk 2026 berhasil diinputkan!');
    }
}
