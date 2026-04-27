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
        Schema::table('trbreakdownmenudt', function (Blueprint $table) {
            $table->string('trbreakdownmenudt_note', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trbreakdownmenudt', function (Blueprint $table) {
            $table->decimal('trbreakdownmenudt_note', 15, 2)->nullable()->change();
        });
    }
};
