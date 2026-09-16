<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_talents', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->nullable();
            $table->string('employee_id')->nullable()->index();
            $table->string('employee_name')->index();
            $table->string('posisi_asal')->nullable(); // Job Title Name
            $table->string('talent_to')->nullable(); // Posisi Target
            $table->string('unit')->nullable()->index();
            $table->string('region')->nullable()->index();
            $table->string('mentor_lapangan')->nullable();
            $table->string('mentor_frlc')->nullable();
            $table->string('status')->nullable()->index(); // Sudah Penugasan, Akan Penugasan, Belum Penugasan, Confirm
            $table->text('keterangan')->nullable(); // Plt. Askep di ATS 1, dll.
            $table->string('periode_penugasan_lama')->nullable();
            $table->text('alasan')->nullable();
            $table->string('periode_penugasan_baru')->nullable();
            $table->string('lokasi_penugasan_baru')->nullable();
            $table->text('catatan_tambahan')->nullable();
            $table->string('kategori')->nullable()->index(); // TALENT ESTATE TAHUN 2026, TALENT ESTATE REDEVELOP TAHUN 2025, TALENT MILL TAHUN 2026
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_talents');
    }
};
