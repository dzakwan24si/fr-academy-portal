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
        Schema::create('asisten_program_monitoring', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->nullable();
            $table->string('kode_kategori', 10)->nullable(); // A, B, C, D, E
            $table->string('kategori_program', 150)->nullable(); // Field Assistant Training, etc.
            $table->string('nama_program', 255)->index(); // e.g. Field Assistant - I/2002
            $table->integer('tahun_rekrut')->nullable();
            $table->integer('tahun_training')->nullable();
            $table->integer('tahun_lulus')->nullable();
            $table->integer('jumlah_trainee')->default(0);
            $table->integer('jumlah_culled')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asisten_program_monitoring');
    }
};
