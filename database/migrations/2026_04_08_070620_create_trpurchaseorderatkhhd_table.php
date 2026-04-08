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
        Schema::create('trpurchaseorderatkhhd', function (Blueprint $table) {
            $table->string('purchase_order_atk_id')->primary();
            $table->date('purchase_order_atk_date');
            $table->string('purchase_order_atk_supplier_id')->nullable();
            $table->string('purchase_order_atk_supplier_name')->nullable();
            $table->string('purchase_order_atk_pic_name')->nullable();
            $table->string('purchase_order_atk_pic_phone')->nullable();
            $table->string('purchase_order_atk_to')->nullable();
            $table->text('purchase_order_atk_address')->nullable();
            $table->text('purchase_order_atk_note')->nullable();
            $table->decimal('purchase_order_atk_discount', 15, 2)->default(0);
            $table->decimal('purchase_order_atk_tax', 5, 2)->default(0);
            $table->decimal('purchase_order_atk_tax_amount', 15, 2)->default(0);
            $table->decimal('purchase_order_atk_koefisien', 10, 2)->default(0);
            $table->decimal('purchase_order_atk_budget', 15, 2)->default(0);
            $table->decimal('purchase_order_atk_budget_over', 15, 2)->default(0);
            $table->decimal('purchase_order_atk_subtotal', 15, 2)->default(0);
            $table->decimal('purchase_order_atk_grandtotal', 15, 2)->default(0);
            $table->datetime('upddate')->nullable();
            $table->string('upduser')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trpurchaseorderatkhhd');
    }
};
