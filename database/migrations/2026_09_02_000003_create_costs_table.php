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
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');            // 1–12
            $table->string('kategori');                      // e.g. ATK, Konsumsi, Transportasi
            $table->string('sub_kategori')->nullable();
            $table->string('nama_item');
            $table->string('keterangan')->nullable();
            $table->decimal('budget', 15, 2)->default(0);   // planned amount
            $table->decimal('realisasi', 15, 2)->default(0); // actual amount
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
