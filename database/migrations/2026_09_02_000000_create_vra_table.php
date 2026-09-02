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
            $table->unsignedTinyInteger('bulan')->comment('1-12');
            $table->decimal('upah_bulan', 15, 2)->default(0);
            $table->decimal('upah_tahun', 15, 2)->default(0);
            $table->decimal('BBM_bulan', 15, 2)->default(0);
            $table->decimal('BBM_tahun', 15, 2)->default(0);
            $table->decimal('preventif_maintenance_bulan', 15, 2)->default(0);
            $table->decimal('preventif_maintenance_tahun', 15, 2)->default(0);
            $table->decimal('biaya_lainnya_bulan', 15, 2)->default(0);
            $table->decimal('biaya_lainnya_tahun', 15, 2)->default(0);
            $table->decimal('total_biaya_bulan', 15, 2)->default(0);
            $table->decimal('total_biaya_tahun', 15, 2)->default(0);
            $table->decimal('KM_awal', 12, 2)->default(0);
            $table->decimal('KM_akhir', 12, 2)->default(0);
            $table->decimal('KM_bulan', 12, 2)->default(0);
            $table->decimal('KM_tahun', 12, 2)->default(0);
            $table->smallInteger('tahun')->index();
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
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
