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
        Schema::create('profil_talents', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->nullable();
            $table->string('employee_id')->nullable()->index();
            $table->string('nama')->index();
            $table->text('profilling')->nullable();
            $table->string('umur')->nullable();
            $table->string('alumni')->nullable();
            $table->string('status_pernikahan')->nullable();
            $table->string('agama')->nullable();
            $table->string('talent')->nullable()->index(); // Estate, Mill, Traksi, Redevelop
            $table->string('jabatan')->nullable();
            $table->string('talent_to')->nullable();
            $table->string('status_ojt')->nullable();
            $table->string('lokasi_ojt')->nullable();
            $table->string('region')->nullable()->index();
            $table->string('mentor')->nullable();
            $table->string('mentor_frlc')->nullable();
            $table->string('masa_kerja')->nullable();
            $table->text('histori_pekerjaan')->nullable();
            $table->text('achievement')->nullable();
            $table->text('keterangan_penugasan')->nullable();
            $table->string('periode_penugasan')->nullable();
            $table->text('alasan')->nullable();

            // Progress & Bobot Pelatihan
            $table->float('refreshment')->nullable();
            $table->float('softskill')->nullable();
            $table->float('ojt')->nullable();
            $table->float('overall_progress')->nullable();
            $table->string('teknis')->nullable();
            $table->string('leadership')->nullable();
            $table->string('training')->nullable();

            // Catatan & Saran Umum
            $table->text('catatan_mentor_general')->nullable();
            $table->text('development_suggestion_general')->nullable();

            // Strength & Weakness
            $table->text('strength')->nullable();
            $table->text('weakness')->nullable();

            // Nilai Akhir
            $table->float('nilai_ojt')->nullable();
            $table->float('nilai_evaluasi_panelis')->nullable();
            $table->float('nilai_akhir')->nullable();
            $table->text('keterangan_evaluasi')->nullable();
            $table->text('foto_profil')->nullable();

            // Kolom Bulanan 12 Bulan (Januari s.d. Desember)
            $months = [
                'januari', 'februari', 'maret', 'april', 'mei', 'juni',
                'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
            ];

            foreach ($months as $m) {
                $table->float("nilai_{$m}")->nullable();
                $table->text("catatan_mentor_{$m}")->nullable();
                $table->text("dev_suggestion_{$m}")->nullable();
            }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_talents');
    }
};
