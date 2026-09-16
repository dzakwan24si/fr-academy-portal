<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asisten', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->nullable();

            // 1. Identitas Pribadi
            $table->string('nik')->nullable();
            $table->string('nama');
            $table->string('jenis_kelamin')->nullable(); // L / P
            $table->string('tempat_lahir')->nullable();
            $table->string('tanggal_lahir')->nullable();
            $table->integer('usia')->nullable();
            $table->string('agama')->nullable();
            $table->string('status_pernikahan')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat_domisili')->nullable();
            $table->string('no_ktp')->nullable();

            // 2. Pendidikan
            $table->string('jenjang_pendidikan')->nullable(); // S1, D3, D4, S2
            $table->string('universitas')->nullable();
            $table->string('fakultas')->nullable();
            $table->string('jurusan')->nullable();
            $table->decimal('ipk', 4, 2)->nullable();
            $table->integer('tahun_lulus')->nullable();

            // 3. Rekrutmen & Program Training
            $table->string('asal_rekrut')->nullable();
            $table->string('jalur_rekrutmen')->nullable();
            $table->string('program')->nullable();
            $table->string('batch')->nullable();
            $table->string('tanggal_mulai_training')->nullable();
            $table->string('tanggal_selesai_training')->nullable();
            $table->integer('tahun_angkatan')->nullable();

            // 4. Penilaian Seleksi
            $table->decimal('nilai_psikotest', 6, 2)->nullable();
            $table->decimal('nilai_interview_hr', 6, 2)->nullable();
            $table->decimal('nilai_interview_user', 6, 2)->nullable();
            $table->string('status_mcu')->nullable();
            $table->integer('ranking_seleksi')->nullable();

            // 5. Progress & Kelulusan Training
            $table->decimal('nilai_akademik', 6, 2)->nullable();
            $table->decimal('nilai_praktek', 6, 2)->nullable();
            $table->decimal('nilai_sikap', 6, 2)->nullable();
            $table->decimal('nilai_akhir_training', 6, 2)->nullable();
            $table->string('status_kelulusan')->nullable(); // Lulus, Tidak Lulus, Resign saat Training
            $table->string('tanggal_kelulusan')->nullable();

            // 6. Penempatan Awal (Setelah Training)
            $table->string('jabatan_awal')->nullable(); // Posisi Awal setelah training
            $table->string('pt_penempatan')->nullable();
            $table->string('regional_penempatan_training')->nullable();
            $table->string('unit_kerja_penempatan_training')->nullable();
            $table->string('tanggal_sk_awal')->nullable();

            // 7. Kepegawaian Saat Ini
            $table->string('status_karyawan')->nullable(); // PKWT, PKWTT, Resign, Mutasi
            $table->string('jabatan_terakhir')->nullable(); // Posisi saat ini / terakhir
            $table->string('level_jabatan')->nullable();
            $table->string('region_saat_ini')->nullable(); // Region (Kepegawaian Saat Ini)
            $table->string('unit_kerja_saat_ini')->nullable(); // Unit Kerja (Kepegawaian Saat Ini)
            $table->string('atasan_langsung')->nullable();
            $table->string('tanggal_pengangkatan')->nullable();

            // 8. Resign & Ijazah
            $table->string('status_resign')->nullable(); // Aktif, Resign, PHK
            $table->string('tanggal_resign')->nullable();
            $table->text('alasan_resign')->nullable();
            $table->string('status_ijazah')->nullable(); // Ditahan, Diserahkan, Selesai Ikatan Dinas
            $table->string('tanggal_pengambilan_ijazah')->nullable();
            $table->string('no_tanda_terima_ijazah')->nullable();

            // 9. Lainnya
            $table->string('no_bpjs_tk')->nullable();
            $table->string('no_bpjs_kes')->nullable();
            $table->string('npwp')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asisten');
    }
};
