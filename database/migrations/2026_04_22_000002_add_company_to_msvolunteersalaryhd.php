<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('msvolunteersalaryhd', function (Blueprint $table) {
            $table->string('volunteer_salary_hd_company_id')->nullable();
            $table->string('volunteer_salary_hd_company_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('msvolunteersalaryhd', function (Blueprint $table) {
            $table->dropColumn(['volunteer_salary_hd_company_id', 'volunteer_salary_hd_company_name']);
        });
    }
};
