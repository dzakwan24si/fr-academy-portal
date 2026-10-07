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
        Schema::create('trainee_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daily_class_id')->constrained()->cascadeOnDelete();
            $table->json('answers');
            $table->text('explanation_de')->nullable(); // Optional explanation for D & E
            $table->text('kesan_pesan'); // Required feedback
            $table->timestamps();

            // A trainee can only submit one evaluation per session
            $table->unique(['trainee_id', 'daily_class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainee_evaluations');
    }
};
