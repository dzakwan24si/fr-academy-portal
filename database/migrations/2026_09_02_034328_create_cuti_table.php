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
        Schema::create('cuti', function (Blueprint $table) {
            $table->id('cuti_id');
            $table->string('nik', 50);
            $table->string('nama', 150);
            $table->string('jabatan', 100)->nullable();
            $table->string('jenis_jabatan', 50)->nullable();
            $table->date('tmk')->nullable(); // Tanggal Mulai Kerja
            $table->integer('tahun'); // Periode tahun aktif (misal 2026)
            
            // Hak Cuti (Hak cuti tahun Y-1, diinput manual dengan default)
            $table->integer('hak_cuti')->default(12);
            
            // Sisa Hak Cuti Carryover (2 tahun sebelumnya, default 0 atau dihitung dari histori)
            $table->integer('sisa_hak_cuti_carryover')->default(0);
            
            // Total cuti yang sudah diambil di tahun tersebut
            $table->integer('cuti_diambil')->default(0);
            
            // Penggunaan cuti per bulan (Periode Pengambilan Cuti)
            $table->integer('jan')->default(0);
            $table->integer('feb')->default(0);
            $table->integer('mar')->default(0);
            $table->integer('apr')->default(0);
            $table->integer('mei')->default(0);
            $table->integer('jun')->default(0);
            $table->integer('jul')->default(0);
            $table->integer('agu')->default(0);
            $table->integer('sep')->default(0);
            $table->integer('okt')->default(0);
            $table->integer('nov')->default(0);
            $table->integer('des')->default(0);
            
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Unique constraint: 1 karyawan hanya punya 1 baris per tahun
            $table->unique(['nik', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuti');
    }
};
