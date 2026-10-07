<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Fix program_type in training_records
        DB::table('training_records')
            ->where('program_type', 'Non reguler')
            ->update(['program_type' => 'non_reguler']);

        // 2. Backfill tanggal_pelaksanaan in program_non_reguler
        $classes = DB::table('daily_classes')->get();
        foreach ($classes as $c) {
            if (! $c->date) {
                continue;
            }

            $s = Carbon::parse($c->date);
            $e = $c->end_date ? Carbon::parse($c->end_date) : $s;
            $t = $s->translatedFormat('d M Y');
            if ($c->end_date && $s->format('Y-m-d') !== $e->format('Y-m-d')) {
                $t .= ' - '.$e->translatedFormat('d M Y');
            }

            $exists = DB::table('program_non_reguler')
                ->where('id_nonreguler', $c->id)
                ->exists();

            if ($exists) {
                DB::table('program_non_reguler')
                    ->where('id_nonreguler', $c->id)
                    ->update([
                        'tanggal_pelaksanaan' => json_encode([$t]),
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for data cleanup
    }
};
