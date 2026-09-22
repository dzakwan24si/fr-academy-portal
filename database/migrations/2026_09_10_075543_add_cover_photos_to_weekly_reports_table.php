<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weekly_reports', function (Blueprint $table) {
            // 4 diamond photos for cover page 1
            $table->string('cover_photo_1')->nullable()->after('quote');
            $table->string('cover_photo_2')->nullable()->after('cover_photo_1');
            $table->string('cover_photo_3')->nullable()->after('cover_photo_2');
            $table->string('cover_photo_4')->nullable()->after('cover_photo_3');
            // 1 main/big photo for page 2 (strip & big photo below "REGULER")
            $table->string('cover_photo_main')->nullable()->after('cover_photo_4');
        });
    }

    public function down(): void
    {
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->dropColumn([
                'cover_photo_1', 'cover_photo_2', 'cover_photo_3',
                'cover_photo_4', 'cover_photo_main',
            ]);
        });
    }
};
