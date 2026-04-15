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
        Schema::create('trabsensihd', function (Blueprint $table) {
            $table->string('tr_absensi_header_code')->primary();
            $table->date('tr_absensi_header_date');
            $table->string('tr_absensi_header_name');
            $table->string('tr_absensi_header_branch')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabsensihd');
    }
};
