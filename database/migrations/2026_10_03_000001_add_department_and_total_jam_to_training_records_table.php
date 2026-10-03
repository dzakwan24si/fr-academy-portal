<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_records', function (Blueprint $table) {
            $table->string('department')->nullable()->after('company');
            $table->string('jml_hari')->nullable()->after('tahun');
            $table->string('total_jam')->nullable()->after('jml_hari');
        });
    }

    public function down(): void
    {
        Schema::table('training_records', function (Blueprint $table) {
            $table->dropColumn(['department', 'jml_hari', 'total_jam']);
        });
    }
};
