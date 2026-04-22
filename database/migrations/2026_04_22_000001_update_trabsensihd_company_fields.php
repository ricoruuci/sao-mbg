<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trabsensihd', function (Blueprint $table) {
            $table->dropColumn('tr_absensi_header_branch');
            $table->string('tr_absensi_header_company_id')->nullable();
            $table->string('tr_absensi_header_company_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trabsensihd', function (Blueprint $table) {
            $table->dropColumn(['tr_absensi_header_company_id', 'tr_absensi_header_company_name']);
            $table->string('tr_absensi_header_branch')->nullable();
        });
    }
};
