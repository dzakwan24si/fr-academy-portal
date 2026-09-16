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
        Schema::create('mpp', function (Blueprint $table) {
            $table->id('mpp_id');
            $table->string('nama_jabatan', 100);
            $table->string('jenis_jabatan', 100); // Staff, PBT, KHT, KHL
            $table->integer('jumlah')->default(0);
            $table->integer('tahun');
            $table->tinyInteger('bulan'); // 1 - 12
            $table->timestamps();

            // Unique combination per jabatan, jenis_jabatan, tahun, and bulan
            $table->unique(['nama_jabatan', 'jenis_jabatan', 'tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpp');
    }
};
