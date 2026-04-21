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
        Schema::table('TrBeliBBHd', function (Blueprint $table) {
            $table->string('rekening_beli', 100)->nullable()->after('date_costing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('TrBeliBBHd', function (Blueprint $table) {
            $table->dropColumn('rekening_beli');
        });
    }
};
