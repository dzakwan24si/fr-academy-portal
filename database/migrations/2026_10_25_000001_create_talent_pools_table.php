<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talent_pools', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->nullable();
            $table->string('employee_id')->nullable()->index();
            $table->string('employee_name')->index();
            $table->string('posisi_asal')->nullable(); // Posisi saat ini
            $table->string('talent_to')->nullable(); // Posisi Target / Proyeksi
            $table->string('unit')->nullable()->index();
            $table->string('region')->nullable()->index();
            $table->string('mentor_lapangan')->nullable();
            $table->string('mentor_frlc')->nullable();
            $table->string('status')->default('Belum Penempatan')->index(); // 'Sudah penempatan' atau 'Belum Penempatan'
            $table->text('keterangan')->nullable();
            $table->string('lokasi_penempatan')->nullable();
            $table->date('tanggal_penempatan')->nullable();
            $table->string('kategori')->nullable()->index(); // Batch / Program Talent Pool
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talent_pools');
    }
};
