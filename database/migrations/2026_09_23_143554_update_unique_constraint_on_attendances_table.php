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
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['daily_class_id']);
            $table->dropUnique('attendances_daily_class_id_trainee_id_unique');
            $table->unique(['daily_class_id', 'trainee_id', 'attendance_date'], 'attendances_class_trainee_date_unique');
            $table->foreign('daily_class_id')->references('id')->on('daily_classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['daily_class_id']);
            $table->dropUnique('attendances_class_trainee_date_unique');
            $table->unique(['daily_class_id', 'trainee_id'], 'attendances_daily_class_id_trainee_id_unique');
            $table->foreign('daily_class_id')->references('id')->on('daily_classes')->onDelete('cascade');
        });
    }
};

