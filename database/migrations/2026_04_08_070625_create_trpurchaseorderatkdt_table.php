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
        Schema::create('trpurchaseorderatkdt', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_order_atk_id');
            $table->string('purchase_order_atk_detail_itemid')->nullable();
            $table->string('purchase_order_atk_detail_itemname')->nullable();
            $table->text('purchase_order_atk_detail_formula')->nullable();
            $table->decimal('purchase_order_atk_detail_qty', 15, 2)->default(0);
            $table->decimal('purchase_order_atk_detail_qty_invoice', 15, 2)->default(0);
            $table->string('purchase_order_atk_detail_uom')->nullable();
            $table->decimal('purchase_order_atk_detail_last_price', 15, 2)->default(0);
            $table->decimal('purchase_order_atk_detail_price', 15, 2)->default(0);
            $table->decimal('purchase_order_atk_detail_total', 15, 2)->default(0);
            $table->date('purchase_order_atk_detail_send_date')->nullable();
            $table->datetime('upddate')->nullable();
            $table->string('upduser')->nullable();

            $table->foreign('purchase_order_atk_id')->references('purchase_order_atk_id')->on('trpurchaseorderatkhhd')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trpurchaseorderatkdt');
    }
};
