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
        Schema::create('daily_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('title');
            $table->string('trainer_name');
            $table->string('qr_code_token')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_class_id')->constrained()->onDelete('cascade');
            $table->foreignId('trainee_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha'])->default('alpha');
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
            
            // A trainee can only have one attendance record per daily class
            $table->unique(['daily_class_id', 'trainee_id']);
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_class_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->dateTime('deadline');
            $table->timestamps();
        });

        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('trainee_id')->constrained()->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->enum('status', ['belum', 'sudah'])->default('belum');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // A trainee can only submit once per task (or at least one record per task)
            $table->unique(['task_id', 'trainee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_classes_and_related_tables');
    }
};
