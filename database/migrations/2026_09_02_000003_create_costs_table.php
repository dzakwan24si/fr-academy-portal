<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->id('cost_id');
            $table->string('GL_ACCOUNT', 255)->nullable();
            $table->string('jenis_cost', 100)->nullable();
            $table->string('account_name', 150)->nullable();
            $table->unsignedSmallInteger('tahun')->default(2026);
            $table->unsignedTinyInteger('bulan')->nullable();            // 1–12
            $table->string('kategori')->nullable();                      // e.g. ATK, Konsumsi, Transportasi
            $table->string('sub_kategori')->nullable();
            $table->string('nama_item')->nullable();
            $table->string('keterangan')->nullable();
            $table->decimal('budget', 20, 2)->nullable()->default(0);   // planned amount
            $table->decimal('realisasi', 20, 2)->nullable()->default(0); // actual amount
            $table->decimal('budget_tahunan', 20, 2)->nullable()->default(0);
            $table->date('tanggal')->nullable();
            $table->string('no_bukti')->nullable();          // receipt / voucher number
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costs');
    }
};
