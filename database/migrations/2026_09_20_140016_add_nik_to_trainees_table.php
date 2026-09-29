<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->string('nik')->nullable()->after('id');
        });

        // Add dummy NIK to existing data
        $trainees = DB::table('trainees')->get();
        $startNik = 100000;
        foreach ($trainees as $trainee) {
            DB::table('trainees')
                ->where('id', $trainee->id)
                ->update(['nik' => (string) $startNik++]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->dropColumn('nik');
        });
    }
};
