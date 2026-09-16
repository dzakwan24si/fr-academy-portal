<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. RAB Table
        Schema::create('capex_rab', function (Blueprint $table) {
            $table->id('rab_id');
            $table->unsignedSmallInteger('tahun')->default(2026)->index();
            $table->string('lokasi', 50)->default('kantor')->index(); // 'kantor', 'mess'
            $table->string('kel_barang_kapital', 255);
            $table->string('foto_barang', 255)->nullable();
            $table->string('type', 255)->nullable();
            $table->string('satuan', 50)->nullable();
            $table->decimal('jumlah_unit', 12, 2)->nullable()->default(0);
            $table->decimal('harga_satuan', 15, 2)->nullable()->default(0);
            $table->decimal('total_rp', 15, 2)->nullable()->default(0);
            $table->timestamps();

            $table->index(['tahun', 'lokasi']);
        });

        // 2. Realisasi Table
        Schema::create('capex_realisasi', function (Blueprint $table) {
            $table->id('realisasi_id');
            $table->unsignedBigInteger('rab_id')->nullable()->index();
            $table->unsignedSmallInteger('tahun')->default(2026)->index();
            $table->string('lokasi', 50)->default('kantor')->index(); // 'kantor', 'mess'
            $table->string('kel_barang_kapital', 255)->nullable();
            $table->string('foto_barang', 255)->nullable();
            $table->string('nama_barang', 255)->nullable(); // deskripsi transaksi / invoice item
            $table->string('type', 255)->nullable();
            $table->date('tanggal')->nullable()->index();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('total_rp', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['tahun', 'lokasi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capex_realisasi');
        Schema::dropIfExists('capex_rab');
    }
};
