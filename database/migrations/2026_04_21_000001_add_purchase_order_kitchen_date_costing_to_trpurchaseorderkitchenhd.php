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
        Schema::table('trpurchaseorderkitchenhd', function (Blueprint $table) {
            $table->date('purchase_order_kitchen_date_costing')->nullable()->after('purchase_order_kitchen_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trpurchaseorderkitchenhd', function (Blueprint $table) {
            $table->dropColumn('purchase_order_kitchen_date_costing');
        });
    }
};
