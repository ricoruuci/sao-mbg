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
        Schema::create('msvolunteersalarydt', function (Blueprint $table) {
            $table->string('volunteer_salary_hd_code', 50);
            $table->string('volunteer_salary_dt_user_code', 50);
            $table->string('volunteer_salary_dt_user_name', 100);
            $table->string('volunteer_salary_dt_divisi', 50)->nullable();
            $table->integer('volunteer_salary_dt_work_day')->nullable();
            $table->decimal('volunteer_salary_dt_price', 15, 2)->nullable();
            $table->decimal('volunteer_salary_dt_bonuses', 15, 2)->nullable();
            $table->decimal('volunteer_salary_dt_total', 15, 2)->nullable();
            $table->primary(['volunteer_salary_hd_code', 'volunteer_salary_dt_user_code']);
            $table->foreign('volunteer_salary_hd_code')->references('volunteer_salary_hd_code')->on('msvolunteersalaryhd')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('msvolunteersalarydt');
    }
};
