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
        Schema::table('trainees', function (Blueprint $table) {
            $table->string('ojt_company_name')->nullable();
            $table->string('ojt_region')->nullable();
            $table->string('ojt_location_code')->nullable();
            $table->date('ojt_start_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->dropColumn(['ojt_company_name', 'ojt_region', 'ojt_location_code', 'ojt_start_date']);
        });
    }
};
