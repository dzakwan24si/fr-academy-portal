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
            $table->date('date')->nullable()->change();
            $table->string('trainer_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_classes', function (Blueprint $table) {
            // Note: going back to not-null might fail if there are null values
            $table->date('date')->nullable(false)->change();
            $table->string('trainer_name')->nullable(false)->change();
        });
    }
};
