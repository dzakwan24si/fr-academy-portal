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
        Schema::table('daily_classes', function (Blueprint $table) {
            $table->boolean('is_evaluation_open')->default(false)->after('trainer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_classes', function (Blueprint $table) {
            $table->dropColumn('is_evaluation_open');
        });
    }
};
