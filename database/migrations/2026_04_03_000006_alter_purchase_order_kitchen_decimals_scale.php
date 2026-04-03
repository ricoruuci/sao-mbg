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
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_discount DECIMAL(15, 6) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_tax DECIMAL(15, 6) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_tax_amount DECIMAL(15, 6) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_koefisien DECIMAL(15, 6) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_budget DECIMAL(15, 6) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_budget_over DECIMAL(15, 6) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_subtotal DECIMAL(15, 6) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_grandtotal DECIMAL(15, 6) NOT NULL");

        DB::statement("ALTER TABLE trpurchaseorderkitchendt ALTER COLUMN purchase_order_kitchen_detail_formula DECIMAL(15, 6) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchendt ALTER COLUMN purchase_order_kitchen_detail_qty DECIMAL(15, 6) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchendt ALTER COLUMN purchase_order_kitchen_detail_qty_invoice DECIMAL(15, 6) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchendt ALTER COLUMN purchase_order_kitchen_detail_last_price DECIMAL(15, 6) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchendt ALTER COLUMN purchase_order_kitchen_detail_price DECIMAL(15, 6) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchendt ALTER COLUMN purchase_order_kitchen_detail_total DECIMAL(15, 6) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_discount DECIMAL(15, 2) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_tax DECIMAL(5, 2) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_tax_amount DECIMAL(15, 2) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_koefisien DECIMAL(15, 2) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_budget DECIMAL(15, 2) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_budget_over DECIMAL(15, 2) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_subtotal DECIMAL(15, 2) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchenhd ALTER COLUMN purchase_order_kitchen_grandtotal DECIMAL(15, 2) NOT NULL");

        DB::statement("ALTER TABLE trpurchaseorderkitchendt ALTER COLUMN purchase_order_kitchen_detail_formula DECIMAL(15, 2) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchendt ALTER COLUMN purchase_order_kitchen_detail_qty DECIMAL(15, 2) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchendt ALTER COLUMN purchase_order_kitchen_detail_qty_invoice DECIMAL(15, 2) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchendt ALTER COLUMN purchase_order_kitchen_detail_last_price DECIMAL(15, 2) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchendt ALTER COLUMN purchase_order_kitchen_detail_price DECIMAL(15, 2) NOT NULL");
        DB::statement("ALTER TABLE trpurchaseorderkitchendt ALTER COLUMN purchase_order_kitchen_detail_total DECIMAL(15, 2) NOT NULL");
    }
};
