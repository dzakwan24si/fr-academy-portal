<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_records', function (Blueprint $table) {
            $table->id();
            $table->string('program_type', 20);
            $table->unsignedBigInteger('program_id');
            $table->string('participant_identifier')->nullable();
            $table->string('participant_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('company')->nullable();
            $table->string('work_location')->nullable();
            $table->string('group_name')->nullable();
            $table->string('region')->nullable();
            $table->string('gol')->nullable();
            $table->string('position')->nullable();
            $table->string('instructor')->nullable();
            $table->text('notes')->nullable();
            $table->string('internal_external')->nullable();
            $table->string('level')->nullable();
            $table->string('periode')->nullable();
            $table->string('bulan', 10)->nullable();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->string('tanggal', 50)->nullable();
            $table->date('training_date')->nullable();
            $table->string('status', 20)->default('valid');
            $table->timestamps();

            $table->index(['program_type', 'program_id']);
            $table->index(['training_date', 'status']);
        });

        Schema::create('realisasi_vs_batch', function (Blueprint $table) {
            $table->id('realisasi_id');
            $table->string('program_type', 20);
            $table->unsignedBigInteger('program_id');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');
            $table->unsignedInteger('legacy_realized_batch')->nullable();
            $table->unsignedInteger('legacy_realized_participants')->nullable();
            $table->timestamps();

            $table->unique(['program_type', 'program_id', 'tahun', 'bulan'], 'realisasi_program_period_unique');
            $table->index(['tahun', 'bulan', 'program_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi_vs_batch');
        Schema::dropIfExists('training_records');
    }
};
