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
        Schema::create('msvolunteersalary', function (Blueprint $table) {
            $table->string('volunteer_salary_code', 30)->primary();
            $table->string('volunteer_salary_instansi', 255);
            $table->date('volunteer_salary_date');
            $table->date('volunteer_salary_date_from');
            $table->date('volunteer_salary_date_to');
            $table->string('volunteer_salary_name', 255);
            $table->string('volunteer_salary_position', 150)->nullable();
            $table->decimal('volunteer_salary_price', 15, 6)->default(0);
            $table->decimal('volunteer_salary_qty', 15, 6)->default(0);
            $table->decimal('volunteer_salary_overtime', 15, 6)->default(0);
            $table->decimal('volunteer_salary_total', 15, 6)->default(0);
            $table->dateTime('upddate');
            $table->string('upduser', 50);

            $table->index(['volunteer_salary_date']);
            $table->index(['volunteer_salary_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('msvolunteersalary');
    }
};
