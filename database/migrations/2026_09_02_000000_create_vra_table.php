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
        Schema::create('vra', function (Blueprint $table) {
            $table->id('vra_id');
            $table->string('nomor_polisi', 20);
            $table->string('kendaraan', 100);
            $table->unsignedSmallInteger('tahun')->default(2026)->index();
            $table->unsignedTinyInteger('bulan')->comment('1-12')->index();
            $table->decimal('upah', 15, 2)->default(0);
            $table->decimal('BBM', 15, 2)->default(0);
            $table->decimal('BBM_tahun', 15, 2)->default(0);
            $table->decimal('preventif_maintenance', 15, 2)->default(0);
            $table->decimal('biaya_lainnya', 15, 2)->default(0);
            $table->decimal('total_biaya_bulan', 15, 2)->default(0);
            $table->decimal('KM_awal', 12, 2)->default(0);
            $table->decimal('KM_akhir', 12, 2)->default(0);
            $table->decimal('KM', 12, 2)->default(0);
            $table->decimal('BBM_liter', 12, 2)->default(0);
            $table->decimal('BBM_KM_liter', 12, 2)->default(0);
            $table->decimal('VRA', 15, 2)->default(0);
            $table->decimal('budget_tahun', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['tahun', 'bulan', 'nomor_polisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vra');
    }
};
