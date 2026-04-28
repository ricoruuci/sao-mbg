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
        Schema::create('trspm_dt', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trspm_hd_id');
            $table->string('trspm_dt_user_code', 50)->nullable();
            $table->string('trspm_dt_user_name', 100)->nullable();
            $table->string('trspm_dt_divisi', 100)->nullable();
            $table->integer('trspm_dt_work_day')->default(0);
            $table->decimal('trspm_dt_price', 18, 2)->default(0);
            $table->decimal('trspm_dt_bonuses', 18, 2)->default(0);
            $table->decimal('trspm_dt_overtime', 18, 2)->default(0);
            $table->decimal('trspm_dt_total', 18, 2)->default(0);
            $table->dateTime('upddate')->useCurrent();
            $table->string('upduser', 50)->nullable();

            $table->foreign('trspm_hd_id')->references('id')->on('trspm_hd')->onDelete('cascade');
            $table->index('trspm_hd_id');
            $table->index('trspm_dt_user_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trspm_dt');
    }
};
