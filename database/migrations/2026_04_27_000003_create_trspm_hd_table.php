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
        Schema::create('trspm_hd', function (Blueprint $table) {
            $table->id();
            $table->string('trspm_hd_code', 50)->nullable();
            $table->dateTime('trspm_hd_date')->nullable();
            $table->dateTime('trspm_hd_date_from')->nullable();
            $table->dateTime('trspm_hd_date_to')->nullable();
            $table->string('trspm_hd_company_id', 50)->nullable();
            $table->string('trspm_hd_company_name', 100)->nullable();
            $table->decimal('trspm_hd_subtotal', 18, 2)->default(0);
            $table->decimal('trspm_hd_subbonuses', 18, 2)->default(0);
            $table->text('trspm_hd_note')->nullable();
            $table->integer('trspm_hd_work_days')->default(0);
            $table->decimal('trspm_hd_overtime_adjustment', 18, 2)->default(0);
            $table->dateTime('upddate')->useCurrent();
            $table->string('upduser', 50)->nullable();

            $table->index('trspm_hd_code');
            $table->index('trspm_hd_date');
            $table->index('trspm_hd_company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trspm_hd');
    }
};
