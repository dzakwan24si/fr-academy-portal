<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_reguler', function (Blueprint $table) {
            if (! Schema::hasColumn('program_reguler', 'kelas')) {
                $table->integer('kelas')->nullable()->after('tahun');
            }
            if (! Schema::hasColumn('program_reguler', 'resign')) {
                $table->integer('resign')->nullable()->after('kelas');
            }
            if (! Schema::hasColumn('program_reguler', 'staff')) {
                $table->integer('staff')->nullable()->after('resign');
            }
            if (! Schema::hasColumn('program_reguler', 'lulus')) {
                $table->integer('lulus')->nullable()->after('staff');
            }
        });
    }

    public function down(): void
    {
        Schema::table('program_reguler', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('program_reguler', 'kelas')) {
                $columns[] = 'kelas';
            }
            if (Schema::hasColumn('program_reguler', 'resign')) {
                $columns[] = 'resign';
            }
            if (Schema::hasColumn('program_reguler', 'staff')) {
                $columns[] = 'staff';
            }
            if (Schema::hasColumn('program_reguler', 'lulus')) {
                $columns[] = 'lulus';
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
