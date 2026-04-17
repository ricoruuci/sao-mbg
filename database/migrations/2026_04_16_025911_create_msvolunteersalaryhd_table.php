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
        Schema::create('msvolunteersalaryhd', function (Blueprint $table) {
            $table->string('volunteer_salary_hd_code', 50)->primary();
            $table->date('volunteer_salary_hd_date');
            $table->date('volunteer_salary_hd_date_from');
            $table->date('volunteer_salary_hd_date_to');
            $table->decimal('volunteer_salary_hd_adjust', 15, 2)->nullable();
            $table->decimal('volunteer_salary_hd_subtotal', 15, 2)->nullable();
            $table->decimal('volunteer_salary_hd_subbonuses', 15, 2)->nullable();
            $table->text('volunteer_salary_hd_note')->nullable();
            $table->timestamp('upddate')->nullable();
            $table->string('upduser', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('msvolunteersalaryhd');
    }
};
