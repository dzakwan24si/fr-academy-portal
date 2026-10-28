<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('talent_pools', function (Blueprint $table) {
            if (! Schema::hasColumn('talent_pools', 'tahun')) {
                $table->integer('tahun')->nullable()->index()->after('kategori');
            }
            if (! Schema::hasColumn('talent_pools', 'jenis_kategori')) {
                $table->string('jenis_kategori')->nullable()->index()->after('tahun');
            }
        });

        // Backfill existing records
        $records = DB::table('talent_pools')->get();
        foreach ($records as $r) {
            $cat = (string) ($r->kategori ?? '');
            $updates = [];

            // Detect year
            if (empty($r->tahun)) {
                if (preg_match('/\b(20\d{2})\b/', $cat, $m)) {
                    $updates['tahun'] = (int) $m[1];
                } elseif (! empty($r->tanggal_penempatan)) {
                    $updates['tahun'] = (int) date('Y', strtotime($r->tanggal_penempatan));
                } else {
                    $updates['tahun'] = (int) date('Y', strtotime($r->created_at ?? 'now'));
                }
            }

            // Detect jenis_kategori
            if (empty($r->jenis_kategori)) {
                $lowerCat = strtolower($cat);
                if (str_contains($lowerCat, 'estate') || str_contains($lowerCat, 'kebun')) {
                    $updates['jenis_kategori'] = 'Estate';
                } elseif (str_contains($lowerCat, 'mill') || str_contains($lowerCat, 'mil') || str_contains($lowerCat, 'pks')) {
                    $updates['jenis_kategori'] = 'Mill';
                } elseif (str_contains($lowerCat, 'traksi')) {
                    $updates['jenis_kategori'] = 'Traksi';
                } elseif (str_contains($lowerCat, 'administrasi') || str_contains($lowerCat, 'admin')) {
                    $updates['jenis_kategori'] = 'Administrasi';
                } elseif (str_contains($lowerCat, 'downstream')) {
                    $updates['jenis_kategori'] = 'Downstream';
                } else {
                    $updates['jenis_kategori'] = 'Estate';
                }
            }

            if (! empty($updates)) {
                DB::table('talent_pools')->where('id', $r->id)->update($updates);
            }
        }
    }

    public function down(): void
    {
        Schema::table('talent_pools', function (Blueprint $table) {
            if (Schema::hasColumn('talent_pools', 'jenis_kategori')) {
                $table->dropColumn('jenis_kategori');
            }
            if (Schema::hasColumn('talent_pools', 'tahun')) {
                $table->dropColumn('tahun');
            }
        });
    }
};
