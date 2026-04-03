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
        Schema::create('trpurchaseorderkitchendt', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_order_kitchen_id', 50);
            $table->string('purchase_order_kitchen_detail_itemid', 50);
            $table->string('purchase_order_kitchen_detail_itemname', 255);
            $table->decimal('purchase_order_kitchen_detail_formula', 15, 6)->default(0);
            $table->decimal('purchase_order_kitchen_detail_qty', 15, 6);
            $table->decimal('purchase_order_kitchen_detail_qty_invoice', 15, 6)->default(0);
            $table->string('purchase_order_kitchen_detail_uom', 50);
            $table->decimal('purchase_order_kitchen_detail_last_price', 15, 6)->default(0);
            $table->decimal('purchase_order_kitchen_detail_price', 15, 6);
            $table->decimal('purchase_order_kitchen_detail_total', 15, 6);
            $table->date('purchase_order_kitchen_detail_send_date');
            $table->dateTime('upddate');
            $table->string('upduser', 50);

            $table->foreign('purchase_order_kitchen_id')
                ->references('purchase_order_kitchen_id')
                ->on('trpurchaseorderkitchenhd')
                ->onDelete('cascade');

            $table->index(['purchase_order_kitchen_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trpurchaseorderkitchendt');
    }
};
