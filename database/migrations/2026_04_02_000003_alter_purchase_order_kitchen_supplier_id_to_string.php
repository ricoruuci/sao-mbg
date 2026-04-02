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
        DB::statement("DROP INDEX trpurchaseorderkitchenhd_purchase_order_kitchen_supplier_id_index ON trpurchaseorderkitchenhd");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_supplier_id VARCHAR(50) NOT NULL");
        DB::statement("CREATE INDEX trpurchaseorderkitchenhd_purchase_order_kitchen_supplier_id_index ON trpurchaseorderkitchenhd (purchase_order_kitchen_supplier_id)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP INDEX trpurchaseorderkitchenhd_purchase_order_kitchen_supplier_id_index ON trpurchaseorderkitchenhd");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_supplier_id INT NOT NULL");
        DB::statement("CREATE INDEX trpurchaseorderkitchenhd_purchase_order_kitchen_supplier_id_index ON trpurchaseorderkitchenhd (purchase_order_kitchen_supplier_id)");
    }
};
