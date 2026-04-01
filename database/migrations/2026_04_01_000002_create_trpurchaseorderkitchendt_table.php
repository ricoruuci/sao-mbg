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
            $table->text('purchase_order_kitchen_detail_description');
            $table->decimal('purchase_order_kitchen_detail_qty', 15, 2);
            $table->string('purchase_order_kitchen_detail_uom', 50);
            $table->decimal('purchase_order_kitchen_detail_price', 15, 2);
            $table->decimal('purchase_order_kitchen_detail_total', 15, 2);
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
