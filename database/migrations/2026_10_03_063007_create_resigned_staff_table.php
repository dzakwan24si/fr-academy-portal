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
        Schema::create('resigned_staff', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->index();
            $table->string('nama');
            $table->date('join_date')->nullable();
            $table->date('resign_date')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('kebun')->nullable();
            $table->text('deskripsi_resign')->nullable();
            $table->string('jenis_resign')->nullable();
            $table->string('cluster_resign')->nullable();
            $table->string('alumni')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('region')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resigned_staff');
    }
};
