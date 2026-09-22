<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('program_non_reguler', 'total_batch') && !Schema::hasColumn('program_non_reguler', 'realisasi_batch')) {
            Schema::table('program_non_reguler', function (Blueprint $table) {
                $table->renameColumn('total_batch', 'realisasi_batch');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('program_non_reguler', 'realisasi_batch') && !Schema::hasColumn('program_non_reguler', 'total_batch')) {
            Schema::table('program_non_reguler', function (Blueprint $table) {
                $table->renameColumn('realisasi_batch', 'total_batch');
            });
        }
    }
};
