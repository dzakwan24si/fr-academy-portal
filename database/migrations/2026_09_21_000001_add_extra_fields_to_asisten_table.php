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
        Schema::table('asisten', function (Blueprint $table) {
            if (!Schema::hasColumn('asisten', 'suku')) {
                $table->string('suku')->nullable()->after('status_pernikahan');
            }
            if (!Schema::hasColumn('asisten', 'latar_belakang_ortu')) {
                $table->string('latar_belakang_ortu')->nullable()->after('suku');
            }
            if (!Schema::hasColumn('asisten', 'no_ijazah')) {
                $table->string('no_ijazah')->nullable()->after('tahun_lulus');
            }
            if (!Schema::hasColumn('asisten', 'periode_pelatihan')) {
                $table->string('periode_pelatihan')->nullable()->after('batch');
            }
            if (!Schema::hasColumn('asisten', 'nilai_krepelin')) {
                $table->string('nilai_krepelin')->nullable()->after('tahun_angkatan');
            }
            if (!Schema::hasColumn('asisten', 'nilai_ist')) {
                $table->string('nilai_ist')->nullable()->after('nilai_krepelin');
            }
            if (!Schema::hasColumn('asisten', 'nilai_disc')) {
                $table->string('nilai_disc')->nullable()->after('nilai_ist');
            }
            if (!Schema::hasColumn('asisten', 'catatan_interview')) {
                $table->text('catatan_interview')->nullable()->after('nilai_disc');
            }
            if (!Schema::hasColumn('asisten', 'disiplin')) {
                $table->string('disiplin')->nullable()->after('catatan_interview');
            }
            if (!Schema::hasColumn('asisten', 'keaktifan')) {
                $table->string('keaktifan')->nullable()->after('disiplin');
            }
            if (!Schema::hasColumn('asisten', 'catatan_trainer')) {
                $table->text('catatan_trainer')->nullable()->after('keaktifan');
            }
            if (!Schema::hasColumn('asisten', 'golongan_awal')) {
                $table->string('golongan_awal')->nullable()->after('jabatan_awal');
            }
            if (!Schema::hasColumn('asisten', 'nik_employee')) {
                $table->string('nik_employee')->nullable()->after('unit_kerja_penempatan_training');
            }
            if (!Schema::hasColumn('asisten', 'golongan_terakhir')) {
                $table->string('golongan_terakhir')->nullable()->after('level_jabatan');
            }
            if (!Schema::hasColumn('asisten', 'penerima_ijazah')) {
                $table->string('penerima_ijazah')->nullable()->after('tanggal_pengambilan_ijazah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asisten', function (Blueprint $table) {
            $table->dropColumn([
                'suku',
                'latar_belakang_ortu',
                'no_ijazah',
                'periode_pelatihan',
                'nilai_krepelin',
                'nilai_ist',
                'nilai_disc',
                'catatan_interview',
                'disiplin',
                'keaktifan',
                'catatan_trainer',
                'golongan_awal',
                'nik_employee',
                'golongan_terakhir',
                'penerima_ijazah',
            ]);
        });
    }
};
