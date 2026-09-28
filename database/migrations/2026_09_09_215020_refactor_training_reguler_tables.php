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
        // 1. Drop old obsolete tables
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('report_participants');
        Schema::dropIfExists('report_photos');

        // 2. Modify existing tables to remove advanced fields from previous migration
//        Schema::table('weekly_reports', function (Blueprint $table) {
//            $table->dropColumn([
//                'ojt_days', 'ojt_location', 'ojt_tujuan', 'ojt_hasil', 'ojt_photos',
//                'inclass_days', 'inclass_location', 'inclass_tujuan', 'inclass_pesan_kesan', 'inclass_photos'
//            ]);
//        });

        Schema::table('trainees', function (Blueprint $table) {
            $table->dropColumn(['birth_place', 'birth_date', 'resign_reason', 'photo_path']);
        });

        // 3. Recreate/Modify quiz_scores table
        Schema::table('quiz_scores', function (Blueprint $table) {
            $table->dropColumn(['practice_score', 'in_class_score', 'average_score', 'ranking', 'input_method']);
            $table->string('materi_name')->after('trainee_id');
            $table->decimal('score', 8, 2)->after('materi_name')->default(0);
        });

        // 4. Create new tables
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_report_id')->constrained()->onDelete('cascade');
            $table->string('session_type'); // orientasi/inclass/ojt/moa
            $table->string('title');
            $table->date('date_start');
            $table->date('date_end');
            $table->string('location')->nullable();
            $table->string('provider')->default('FR Academy');
            $table->string('trainer_name')->nullable();
            $table->text('training_purpose')->nullable();
            $table->text('results_or_impressions')->nullable();
            $table->timestamps();
        });

        Schema::create('session_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('trainee_id')->constrained()->onDelete('cascade');
            $table->string('participation_type')->default('aktif'); // aktif/pasif
            $table->timestamps();
        });

        Schema::create('session_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained()->onDelete('cascade');
            $table->string('category')->nullable(); // For labeling the chart if needed
            $table->decimal('materi_percentage', 5, 2)->default(0);
            $table->decimal('trainer_percentage', 5, 2)->default(0);
            $table->decimal('fasilitas_percentage', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('session_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained()->onDelete('cascade');
            $table->string('photo_path');
            $table->timestamps();
        });

        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_report_id')->constrained()->onDelete('cascade');
            $table->foreignId('trainee_id')->constrained()->onDelete('cascade');
            $table->decimal('average_score', 8, 2)->default(0);
            $table->integer('rank');
            $table->string('rank_group')->nullable(); // top / bottom
            $table->timestamps();
        });

        Schema::create('ojt_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('trainee_id')->constrained()->onDelete('cascade');
            $table->string('location_code');
            $table->string('region')->nullable();
            $table->timestamps();
        });

        Schema::create('resignations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained()->onDelete('cascade');
            $table->string('photo_path')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('reason')->nullable();
            $table->decimal('avg_inclass_score', 8, 2)->default(0);
            $table->integer('inclass_rank')->default(0);
            $table->decimal('avg_practice_score', 8, 2)->default(0);
            $table->integer('practice_rank')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resignations');
        Schema::dropIfExists('ojt_placements');
        Schema::dropIfExists('rankings');
        Schema::dropIfExists('session_photos');
        Schema::dropIfExists('session_evaluations');
        Schema::dropIfExists('session_participants');
        Schema::dropIfExists('training_sessions');

        // Note: Full down rollback logic is omitted for brevity as this is a hard refactor
        // during development. In a real scenario, we would recreate the dropped tables here.
    }
};
