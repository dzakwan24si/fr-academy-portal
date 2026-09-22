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
        Schema::table('trainers', function (Blueprint $table) {
            $table->string('specialization')->nullable()->after('name');
            $table->string('phone')->nullable()->after('specialization');
            $table->string('email')->nullable()->after('phone');
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif')->after('email');
            $table->string('photo_path')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dropColumn(['specialization', 'phone', 'email', 'status', 'photo_path']);
        });
    }
};
