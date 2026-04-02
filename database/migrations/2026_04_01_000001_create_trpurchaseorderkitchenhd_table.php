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
        Schema::create('trpurchaseorderkitchenhd', function (Blueprint $table) {
            $table->string('purchase_order_kitchen_id', 50)->primary();
            $table->date('purchase_order_kitchen_date');
            $table->string('purchase_order_kitchen_supplier_id', 50);
            $table->string('purchase_order_kitchen_supplier_name', 255);
            $table->string('purchase_order_kitchen_pic_name', 255);
            $table->string('purchase_order_kitchen_pic_phone', 50)->nullable();
            $table->string('purchase_order_kitchen_to', 255);
            $table->text('purchase_order_kitchen_address');
            $table->text('purchase_order_kitchen_note')->nullable();
            $table->decimal('purchase_order_kitchen_discount', 15, 2)->default(0);
            $table->decimal('purchase_order_kitchen_tax', 5, 2)->default(0);
            $table->decimal('purchase_order_kitchen_tax_amount', 15, 2)->default(0);
            $table->decimal('purchase_order_kitchen_subtotal', 15, 2)->default(0);
            $table->decimal('purchase_order_kitchen_grandtotal', 15, 2)->default(0);
            $table->dateTime('upddate');
            $table->string('upduser', 50);

            $table->index(['purchase_order_kitchen_date']);
            $table->index(['purchase_order_kitchen_supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trpurchaseorderkitchenhd');
    }
};
