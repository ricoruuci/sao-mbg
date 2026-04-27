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
        Schema::create('trbreakdownmenudt', function (Blueprint $table) {
            $table->string('trbreakdownmenudt_hd_code', 50);
            $table->string('trbreakdownmenudt_itemid', 50);
            $table->decimal('trbreakdownmenudt_qty', 15, 2)->nullable();
            $table->string('trbreakdownmenudt_uomid', 50)->nullable();
            $table->decimal('trbreakdownmenudt_note', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trbreakdownmenudt');
    }
};
