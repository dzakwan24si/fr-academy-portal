<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL enum modification
        DB::statement("ALTER TABLE attendances MODIFY status ENUM('belum', 'hadir', 'sakit', 'izin', 'alpha') DEFAULT 'belum'");

        // Also update all existing 'alpha' to 'belum' to match the new default logic for pending scans
        DB::statement("UPDATE attendances SET status = 'belum' WHERE status = 'alpha' AND scanned_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE attendances MODIFY status ENUM('hadir', 'sakit', 'izin', 'alpha') DEFAULT 'alpha'");
    }
};
