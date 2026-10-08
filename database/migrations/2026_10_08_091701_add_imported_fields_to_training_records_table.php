<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_records', function (Blueprint $table) {
            $table->string('program_type', 20)->nullable()->change();
            $table->unsignedBigInteger('program_id')->nullable()->change();
            $table->string('nama_pelatihan')->nullable();
            $table->string('tempat_pelatihan')->nullable();
            $table->string('reg_non_reg')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('training_records', function (Blueprint $table) {
            $table->dropColumn(['nama_pelatihan', 'tempat_pelatihan', 'reg_non_reg']);
            $table->string('program_type', 20)->nullable(false)->change();
            $table->unsignedBigInteger('program_id')->nullable(false)->change();
        });
    }
};
