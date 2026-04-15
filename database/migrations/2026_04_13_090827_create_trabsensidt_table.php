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
        Schema::create('trabsensidt', function (Blueprint $table) {
            $table->id('tr_absensi_dt_id');
            $table->string('tr_absensi_header_code');
            $table->string('tr_absensi_dt_name');
            $table->date('tr_absensi_dt_date')->nullable();
            $table->time('tr_absensi_dt_clock_in')->nullable();
            $table->time('tr_absensi_dt_clock_out')->nullable();
            $table->decimal('tr_absensi_dt_nominal', 18, 2)->nullable();
            $table->timestamps();
            $table->foreign('tr_absensi_header_code')->references('tr_absensi_header_code')->on('trabsensihd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabsensidt');
    }
};
