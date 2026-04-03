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
            $table->decimal('purchase_order_kitchen_koefisien', 15, 6)->default(0);
            $table->decimal('purchase_order_kitchen_budget', 15, 6)->default(0);
            $table->decimal('purchase_order_kitchen_budget_over', 15, 6)->default(0);
        });

        Schema::table('trpurchaseorderkitchendt', function (Blueprint $table) {
            $table->string('purchase_order_kitchen_detail_itemid', 50)->default('');
            $table->string('purchase_order_kitchen_detail_itemname', 255)->default('');
            $table->decimal('purchase_order_kitchen_detail_formula', 15, 6)->default(0);
            $table->decimal('purchase_order_kitchen_detail_qty_invoice', 15, 6)->default(0);
            $table->decimal('purchase_order_kitchen_detail_last_price', 15, 6)->default(0);

            $table->dropColumn('purchase_order_kitchen_detail_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trpurchaseorderkitchendt', function (Blueprint $table) {
            $table->text('purchase_order_kitchen_detail_description')->nullable();

            $table->dropColumn([
                'purchase_order_kitchen_detail_itemid',
                'purchase_order_kitchen_detail_itemname',
                'purchase_order_kitchen_detail_formula',
                'purchase_order_kitchen_detail_qty_invoice',
                'purchase_order_kitchen_detail_last_price',
            ]);
        });

        Schema::table('trpurchaseorderkitchenhd', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_order_kitchen_koefisien',
                'purchase_order_kitchen_budget',
                'purchase_order_kitchen_budget_over',
            ]);
        });
    }
};
