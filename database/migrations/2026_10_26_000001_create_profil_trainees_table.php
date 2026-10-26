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
        Schema::create('profil_trainees', function (Blueprint $table) {
            $table->id();
            $table->string('trainee_id')->nullable()->index(); // ID Trainee / NIK / Kode Trainee
            $table->string('nama_trainee')->index();
            $table->string('nama_jabatan')->nullable()->index();
            $table->string('asal_universitas')->nullable()->index();
            $table->string('jurusan')->nullable()->index();
            $table->string('jenis_kelamin')->nullable()->index(); // Laki-laki / Perempuan
            $table->integer('usia')->nullable();
            $table->string('suku')->nullable();
            $table->string('agama')->nullable();
            $table->string('pendidikan_terakhir')->nullable()->index();
            $table->decimal('tinggi_badan', 5, 1)->nullable(); // in cm
            $table->decimal('berat_badan', 5, 1)->nullable(); // in kg
            $table->string('daerah_kabupaten_asal')->nullable()->index();
            $table->string('kota_provinsi_asal')->nullable()->index();
            $table->integer('tahun_lulus')->nullable()->index();
            $table->string('masa_studi')->nullable(); // e.g. "4 Tahun", "3.5 Tahun"
            $table->decimal('ipk', 3, 2)->nullable();
            $table->text('pengalaman_organisasi')->nullable();
            $table->text('pengalaman_praktek_kerja_magang')->nullable();
            $table->text('prestasi_diri')->nullable();
            $table->string('hobi')->nullable();
            $table->string('riwayat_kesehatan')->nullable();
            $table->text('catatan_riwayat_kesehatan')->nullable();
            $table->string('pekerjaan_orangtua')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_trainees');
    }
};
