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
        Schema::create('rekrutmen', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->nullable();
            $table->string('lokasi_rekrut');
            $table->enum('metode_rekrutmen', ['Online', 'Offline'])->default('Offline');
            $table->string('program');
            $table->string('tanggal')->nullable();
            $table->integer('tahun')->nullable();
            $table->integer('jumlah_peserta_hadir')->default(0);
            $table->integer('jumlah_lolos_psikotest')->default(0);
            $table->integer('jumlah_hadir_interview_awal')->default(0);
            $table->integer('jumlah_lolos_interview_awal')->default(0);
            $table->integer('jumlah_lolos_interview_user')->default(0);
            $table->integer('jumlah_ikut_mcu')->default(0);
            $table->integer('jumlah_lolos_mcu')->default(0);
            $table->integer('join_fr')->default(0);
            $table->decimal('persen_lulus', 8, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekrutmen');
    }
};
