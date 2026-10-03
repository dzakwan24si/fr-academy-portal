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
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->boolean('is_merged')->default(false)->after('status');
            $table->string('merged_program_names')->nullable()->after('is_merged');
        });

        Schema::table('training_sessions', function (Blueprint $table) {
            $table->string('program_source')->nullable()->after('trainer_photo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->dropColumn('program_source');
        });

        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->dropColumn(['is_merged', 'merged_program_names']);
        });
    }
};
