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
        Schema::table('msvolunteersalaryhd', function (Blueprint $table) {
            $table->dropColumn('volunteer_salary_hd_adjust');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('msvolunteersalaryhd', function (Blueprint $table) {
            $table->decimal('volunteer_salary_hd_adjust', 15, 2)->nullable()->after('volunteer_salary_hd_date_to');
        });
    }
};
