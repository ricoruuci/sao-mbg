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
        DB::statement("\n            UPDATE trpurchaseorderkitchendt\n            SET purchase_order_kitchen_detail_formula = '0'\n            WHERE purchase_order_kitchen_detail_formula IS NULL\n               OR ISNUMERIC(purchase_order_kitchen_detail_formula) = 0\n        ");

        DB::statement("\n            ALTER TABLE trpurchaseorderkitchendt\n            ALTER COLUMN purchase_order_kitchen_detail_formula DECIMAL(15, 6) NOT NULL\n        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("\n            ALTER TABLE trpurchaseorderkitchendt\n            ALTER COLUMN purchase_order_kitchen_detail_formula NVARCHAR(MAX) NULL\n        ");
    }
};
