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
        Schema::table('daily_classes', function (Blueprint $table) {
            $table->string('cert_signer_role')->nullable();
            $table->string('cert_signer_name')->nullable();
            $table->text('cert_description')->nullable();
            $table->date('cert_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_classes', function (Blueprint $table) {
            $table->dropColumn(['cert_signer_role', 'cert_signer_name', 'cert_description', 'cert_date']);
        });
    }
};
