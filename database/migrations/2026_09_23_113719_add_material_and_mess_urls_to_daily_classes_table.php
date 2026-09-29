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
            $table->string('material_url')->nullable();
            $table->string('mess_url')->nullable();
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_classes', function (Blueprint $table) {
            $table->dropColumn(['material_url', 'mess_url']);
            $table->string('material_url')->nullable();
            $table->string('mess_url')->nullable();
            //
        });
    }
};

