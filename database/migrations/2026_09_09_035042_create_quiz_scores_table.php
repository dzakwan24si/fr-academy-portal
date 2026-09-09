<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_report_id')->constrained()->onDelete('cascade');
            $table->foreignId('trainee_id')->constrained()->onDelete('cascade');
            $table->decimal('practice_score', 8, 2)->default(0);
            $table->decimal('in_class_score', 8, 2)->default(0);
            $table->decimal('average_score', 8, 2)->default(0);
            $table->integer('ranking')->nullable();
            $table->string('input_method')->default('manual');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_scores');
    }
};
