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
            $columnsToDrop = [];
            foreach (['university', 'major', 'ojt_company_name', 'ojt_region', 'ojt_location_code'] as $col) {
                if (Schema::hasColumn('trainees', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->string('university')->nullable();
            $table->string('major')->nullable();
            $table->string('ojt_company_name')->nullable();
            $table->string('ojt_region')->nullable();
            $table->string('ojt_location_code')->nullable();
        });
    }
};

