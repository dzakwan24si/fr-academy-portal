<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('portal_programs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('code')->nullable();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('category_key')->default('trainee');
            $table->string('category_name')->nullable();
            $table->string('badge')->nullable();
            $table->boolean('is_reguler')->default(false);
            $table->text('overview')->nullable();
            $table->string('image')->nullable();
            $table->string('hero_image')->nullable();
            $table->json('curriculum_pillars')->nullable();
            $table->json('objectives')->nullable();
            $table->json('learning_method')->nullable();
            $table->string('target_role')->nullable();
            $table->string('duration')->nullable();
            $table->string('format')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed initial data from ProgramCatalogService if available
        if (class_exists(\App\Services\ProgramCatalogService::class)) {
            $initialPrograms = \App\Services\ProgramCatalogService::getAllPrograms();
            $regulerSlugs = ['fat', 'mat', 'tat', 'aat', 'dmt'];
            $order = 1;

            foreach ($initialPrograms as $slug => $data) {
                DB::table('portal_programs')->insert([
                    'slug' => $slug,
                    'code' => $data['code'] ?? strtoupper($slug),
                    'title' => $data['title'],
                    'subtitle' => $data['subtitle'] ?? null,
                    'category_key' => $data['category_key'] ?? 'trainee',
                    'category_name' => $data['category_name'] ?? 'Program Trainee',
                    'badge' => $data['badge'] ?? null,
                    'is_reguler' => in_array($slug, $regulerSlugs),
                    'overview' => $data['overview'] ?? null,
                    'image' => $data['image'] ?? null,
                    'hero_image' => $data['hero_image'] ?? null,
                    'curriculum_pillars' => isset($data['curriculum_pillars']) ? json_encode($data['curriculum_pillars']) : null,
                    'objectives' => isset($data['objectives']) ? json_encode($data['objectives']) : null,
                    'learning_method' => isset($data['learning_method']) ? json_encode($data['learning_method']) : null,
                    'target_role' => $data['target_role'] ?? null,
                    'duration' => $data['duration'] ?? null,
                    'format' => $data['format'] ?? null,
                    'order' => $order++,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_programs');
    }
};
