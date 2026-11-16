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
        Schema::table('profil_trainees', function (Blueprint $table) {
            if (Schema::hasColumn('profil_trainees', 'riwayat_kesehatan')) {
                $table->dropColumn('riwayat_kesehatan');
            }
            if (Schema::hasColumn('profil_trainees', 'catatan_riwayat_kesehatan')) {
                $table->dropColumn('catatan_riwayat_kesehatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_trainees', function (Blueprint $table) {
            $table->string('riwayat_kesehatan')->nullable();
            $table->text('catatan_riwayat_kesehatan')->nullable();
        });
    }
};
