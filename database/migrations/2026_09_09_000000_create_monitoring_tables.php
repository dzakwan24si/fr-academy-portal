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
        // 1. Table Monitoring Angkatan
        Schema::create('monitoring_angkatan', function (Blueprint $table) {
            $table->id('id_angkatan');
            $table->string('angkatan');
            $table->integer('jumlah_awal')->default(0);
            $table->integer('lulus')->default(0);
            $table->integer('resign_in_class')->default(0);
            $table->integer('resign_ojt')->default(0);
            $table->integer('tidak_lulus')->default(0);
            $table->timestamps();
        });

        // 2. Table Monitoring Culled
        Schema::create('monitoring_culled', function (Blueprint $table) {
            $table->id('id_culled');
            $table->unsignedBigInteger('id_angkatan')->nullable();
            $table->string('nama');
            $table->text('alasan')->nullable();
            $table->string('kategori_status')->default('Culled')->nullable();
            $table->string('jenis_culled')->nullable();
            $table->timestamps();

            $table->foreign('id_angkatan')
                ->references('id_angkatan')
                ->on('monitoring_angkatan')
                ->onDelete('set null');
        });

        // 3. Table Monitoring Employees
        Schema::create('monitoring_employees', function (Blueprint $table) {
            $table->id('id_monitoring');
            $table->string('nik')->nullable();
            $table->string('nama');
            $table->string('join_date')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('level_jabatan')->nullable();
            $table->string('angkatan_fr_academy')->nullable();
            $table->string('kategori')->nullable();
            $table->string('alumni')->nullable();
            $table->string('suku')->nullable();
            $table->string('agama')->nullable();
            $table->string('lokasi_terakhir')->nullable();
            $table->string('region_terakhir')->nullable();
            $table->string('status')->nullable();
            $table->string('date_terminate')->nullable();
            $table->string('bulan_terminate')->nullable();
            $table->string('tahun_terminate')->nullable();
            $table->string('lama_bekerja_bulan')->nullable();
            $table->string('lama_bekerja_tahun')->nullable();
            $table->string('lama_bekerja')->nullable();
            $table->text('alasan_resign')->nullable();
            $table->text('detail')->nullable();
            $table->string('lokasi_awal_penempatan')->nullable();
            $table->string('region_awal_penempatan')->nullable();
            $table->string('gm')->nullable();
            $table->string('mk')->nullable();
            $table->timestamps();
        });

        // 4. Table Monitoring Afdeling
        Schema::create('monitoring_afdeling', function (Blueprint $table) {
            $table->id('afeliding_id');
            $table->string('region');
            $table->integer('total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_employees');
        Schema::dropIfExists('monitoring_culled');
        Schema::dropIfExists('monitoring_angkatan');
        Schema::dropIfExists('monitoring_afdeling');
    }
};
