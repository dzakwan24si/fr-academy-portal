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
        Schema::create('biaya_rekruit', function (Blueprint $table) {
            $table->id('id_biaya_rekruit');
            $table->smallInteger('tahun')->index();
            $table->date('tanggal');
            $table->string('nama', 255);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->string('jenis_rekruit', 100)->nullable();
            $table->string('penanggung_jawab', 100)->nullable();
            $table->timestamps();

            $table->index(['tahun', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_rekruit');
    }
};
