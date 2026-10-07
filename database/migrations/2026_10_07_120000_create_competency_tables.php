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
        Schema::create('competency_dictionaries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30); // estate / mill (slug, dipakai di URL)
            $table->string('name');
            $table->unsignedSmallInteger('year');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('draft'); // draft / published
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['code', 'year']);
        });

        Schema::create('competency_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dictionary_id')
                ->constrained('competency_dictionaries')
                ->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['dictionary_id', 'name']);
        });

        Schema::create('competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dictionary_id')
                ->constrained('competency_dictionaries')
                ->cascadeOnDelete();
            // Juga nomor urut tampil 1..N. Sengaja tidak unique agar reorder/swap tidak bentrok.
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('name');
            $table->text('definition')->nullable();
            $table->timestamps();

            $table->index(['dictionary_id', 'sort_order']);
        });

        Schema::create('competency_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competency_id')
                ->constrained('competencies')
                ->cascadeOnDelete();
            $table->foreignId('position_id')
                ->constrained('competency_positions')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('level'); // 1-4
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['competency_id', 'position_id', 'level']);
        });

        Schema::create('competency_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competency_id')
                ->constrained('competencies')
                ->cascadeOnDelete();
            $table->string('type', 20); // knowledge / skill / development
            $table->text('body');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['competency_id', 'type', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competency_items');
        Schema::dropIfExists('competency_levels');
        Schema::dropIfExists('competencies');
        Schema::dropIfExists('competency_positions');
        Schema::dropIfExists('competency_dictionaries');
    }
};
