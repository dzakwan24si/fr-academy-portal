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
        DB::statement("ALTER TABLE `attendances` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'tidak_hadir'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `attendances` MODIFY COLUMN `status` ENUM('hadir', 'sakit', 'izin', 'alpha') NOT NULL DEFAULT 'alpha'");
    }
};
