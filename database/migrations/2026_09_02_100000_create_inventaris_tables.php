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
        // 1. Table: barang
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_asset');
            $table->string('satuan')->default('Unit'); // e.g. 'Unit', 'Buah', 'Bh', 'Set', 'Pcs'
            $table->string('jenis_lokasi'); // 'Kantor' / 'Gudang' (non-nullable)
            $table->string('lokasi')->nullable(); // alias / backup column
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 2. Table: pembelian
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->date('tanggal_pembelian');
            $table->integer('jumlah')->default(1);
            $table->string('lokasi_spesifik')->nullable(); // Ruangan di Kantor (e.g. Planter, Meeting, etc.)
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 3. Table: stock_opname
        Schema::create('stock_opname', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->date('tanggal_stock_opname');
            $table->string('status')->default('Selesai'); // 'Selesai' / 'Draf'
            $table->text('catatan_sesi')->nullable();
            $table->string('lokasi_spesifik')->nullable(); // Ruangan di Kantor
            $table->integer('jumlah_baik')->default(0);
            $table->integer('jumlah_rusak')->default(0); // Replacing rusak_ringan, rusak_berat, hilang
            $table->integer('grand_total')->default(0); // jumlah_baik + jumlah_rusak
            $table->text('keterangan')->nullable();
            $table->string('foto_kerusakan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname');
        Schema::dropIfExists('pembelian');
        Schema::dropIfExists('barang');
    }
};
