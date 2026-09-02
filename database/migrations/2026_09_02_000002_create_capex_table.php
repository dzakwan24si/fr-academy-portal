<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capex', function (Blueprint $table) {
            $table->id('capex_id');
            $table->unsignedSmallInteger('tahun');
            $table->enum('jenis_capex', ['kantor', 'mess']);
            $table->enum('jenis_catatan', ['rab', 'realisasi']);
            $table->string('nama_barang');
            $table->string('type_merk')->nullable();
            $table->string('satuan')->nullable();
            // RAB-specific
            $table->decimal('jumlah_unit', 12, 2)->nullable();
            $table->decimal('harga_satuan', 15, 2)->nullable();
            $table->decimal('total_rab', 15, 2)->nullable();
            // Realisasi-specific
            $table->date('tanggal')->nullable();
            $table->string('nama_transaksi')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capex');
    }
};
