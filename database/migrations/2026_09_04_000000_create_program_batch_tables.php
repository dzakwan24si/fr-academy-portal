<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_non_reguler', function (Blueprint $table) {
            $table->id('id_nonreguler');
            $table->string('kategori_pelatihan')->nullable();
            $table->string('jenis_pelatihan');
            $table->string('region')->nullable();
            $table->string('trainer')->nullable();
            $table->string('type_training')->nullable();
            $table->unsignedInteger('total_jam')->nullable();
            $table->unsignedInteger('total_hari')->nullable();
            $table->unsignedInteger('peserta_non_staf')->default(0);
            $table->unsignedInteger('peserta_ast')->default(0);
            $table->unsignedInteger('peserta_askep')->default(0);
            $table->unsignedInteger('peserta_mgr')->default(0);
            $table->unsignedInteger('peserta_gm')->default(0);
            $table->unsignedInteger('peserta_head')->default(0);
            $table->unsignedInteger('target_staf')->default(0);
            $table->unsignedInteger('target_non_staf')->default(0);
            $table->unsignedInteger('target_total')->default(0);
            $table->unsignedInteger('training_staf')->default(0);
            $table->unsignedInteger('training_non_staf')->default(0);
            $table->unsignedInteger('training_total')->default(0);
            $table->unsignedInteger('td_staf')->default(0);
            $table->unsignedInteger('td_non_staf')->default(0);
            $table->unsignedInteger('td_total')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('total_batch')->default(0);
            $table->string('lokasi_training')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('sub_kategori')->nullable();
            $table->json('tanggal_pelaksanaan')->nullable();
            $table->unsignedInteger('TARGET_BATCH')->nullable();
            $table->unsignedInteger('TARGET_PESERTA')->nullable();
            $table->timestamps();
        });

        Schema::create('program_reguler', function (Blueprint $table) {
            $table->id('Program_reguler_id');
            $table->string('Kategori_Program')->nullable();
            $table->string('Training_Program');
            $table->string('Batch')->nullable();
            $table->string('Lokasi_training')->nullable();
            $table->date('Mulai_program')->nullable();
            $table->date('Selesai_program')->nullable();
            $table->unsignedInteger('jumlah_bulan_training')->nullable();
            $table->unsignedInteger('jumlah_hari')->nullable();
            $table->unsignedInteger('Total_training_days')->nullable();
            $table->unsignedInteger('Promosi_mandor')->default(0);
            $table->unsignedInteger('Fresh_Graduate')->default(0);
            $table->unsignedInteger('target_peserta')->default(0);
            $table->decimal('Uang_saku', 15, 2)->default(0);
            $table->string('jenis_program_reguler')->nullable();
            $table->string('Region')->nullable();
            $table->json('tanggal_pelaksanaan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_non_reguler');
        Schema::dropIfExists('program_reguler');
    }
};
