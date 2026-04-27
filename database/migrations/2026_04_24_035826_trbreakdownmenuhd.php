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
        Schema::create('trbreakdownmenuhd', function (Blueprint $table) {
            $table->string('trbreakdownhd_code')->primary();
            $table->date('trbreakdownhd_date');
            $table->decimal('trbreakdownhd_qty_beneficiaries', 15, 2)->nullable();
            $table->string('trbreakdownhd_note')->nullable();
            $table->string('trbreakdownhd_company_id')->nullable();
            $table->string('trbreakdownhd_company_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trbreakdownmenuhd');
    }
};
