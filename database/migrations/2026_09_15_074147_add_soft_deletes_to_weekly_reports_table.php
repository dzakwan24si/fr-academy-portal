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
            $table->softDeletes();
            $table->unsignedBigInteger('parent_report_id')->nullable()->after('is_merged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('parent_report_id');
        });
    }
};
