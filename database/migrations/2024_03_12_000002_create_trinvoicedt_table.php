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
        Schema::create('trinvoicedt', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_code', 50);
            $table->text('invoice_detail_description');
            $table->decimal('invoice_detail_qty', 10, 2);
            $table->decimal('invoice_detail_price', 15, 2);
            $table->decimal('invoice_detail_total', 15, 2);
            $table->datetime('upddate');
            $table->string('upduser', 50);
            
            $table->foreign('invoice_code')->references('invoice_code')->on('trinvoicehd')->onDelete('cascade');
            $table->index(['invoice_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trinvoicedt');
    }
};