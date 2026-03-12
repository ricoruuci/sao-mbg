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
        Schema::create('trinvoicehd', function (Blueprint $table) {
            $table->string('invoice_code', 50)->primary();
            $table->date('invoice_date');
            $table->string('invoice_to', 255);
            $table->text('invoice_note')->nullable();
            $table->decimal('invoice_subtotal', 15, 2)->default(0);
            $table->decimal('invoice_ppn', 5, 2)->default(0);
            $table->decimal('invoice_ppn_amount', 15, 2)->default(0);
            $table->boolean('invoice_ppn_flag')->default(false);
            $table->datetime('upddate');
            $table->string('upduser', 50);
            $table->integer('company_id');
            
            $table->index(['invoice_date']);
            $table->index(['company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trinvoicehd');
    }
};