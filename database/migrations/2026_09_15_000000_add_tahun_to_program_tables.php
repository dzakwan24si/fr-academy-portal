<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('program_non_reguler', 'tahun')) {
            Schema::table('program_non_reguler', function (Blueprint $table) {
                $table->integer('tahun')->nullable();
            });
        }
        if (! Schema::hasColumn('program_reguler', 'tahun')) {
            Schema::table('program_reguler', function (Blueprint $table) {
                $table->integer('tahun')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('program_non_reguler', 'tahun')) {
            Schema::table('program_non_reguler', function (Blueprint $table) {
                $table->dropColumn('tahun');
            });
        }
        if (Schema::hasColumn('program_reguler', 'tahun')) {
            Schema::table('program_reguler', function (Blueprint $table) {
                $table->dropColumn('tahun');
            });
        }
    }
};
