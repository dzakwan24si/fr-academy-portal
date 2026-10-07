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
            $table->string('training_type')->nullable();
            $table->string('topic')->nullable();
            $table->string('location')->nullable();
            $table->string('provider')->nullable()->default('FR Academy');
            $table->text('feedback_notes')->nullable();
            $table->integer('eval_materi_score')->nullable();
            $table->integer('eval_trainer_score')->nullable();
            $table->integer('eval_fasilitas_score')->nullable();
        });

        Schema::table('trainees', function (Blueprint $table) {
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('resign_reason')->nullable();
            $table->string('photo_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->dropColumn([
                'training_type', 'topic', 'location', 'provider',
                'feedback_notes', 'eval_materi_score', 'eval_trainer_score', 'eval_fasilitas_score',
            ]);
        });

        Schema::table('trainees', function (Blueprint $table) {
            $table->dropColumn(['birth_place', 'birth_date', 'resign_reason', 'photo_path']);
        });
    }
};
