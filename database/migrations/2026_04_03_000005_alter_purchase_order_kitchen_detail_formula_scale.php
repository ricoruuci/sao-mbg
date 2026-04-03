<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("\n            ALTER TABLE trpurchaseorderkitchendt\n            ALTER COLUMN purchase_order_kitchen_detail_formula DECIMAL(15, 6) NOT NULL\n        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("\n            ALTER TABLE trpurchaseorderkitchendt\n            ALTER COLUMN purchase_order_kitchen_detail_formula DECIMAL(15, 2) NOT NULL\n        ");
    }
};
