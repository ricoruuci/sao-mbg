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
        DB::statement("ALTER TABLE TrBeliBBDt ALTER COLUMN Harga NUMERIC(19, 6) NULL");
        DB::statement("ALTER TABLE TrBeliBBDt ALTER COLUMN Jml NUMERIC(18, 6) NULL");

        DB::statement("ALTER TABLE TrBeliBBHd ALTER COLUMN DiscAmount NUMERIC(18, 6) NULL");
        DB::statement("ALTER TABLE TrBeliBBHd ALTER COLUMN interest NUMERIC(18, 6) NULL");
        DB::statement("ALTER TABLE TrBeliBBHd ALTER COLUMN STPb NUMERIC(18, 6) NULL");
        DB::statement("ALTER TABLE TrBeliBBHd ALTER COLUMN TTLPb NUMERIC(18, 6) NULL");
        DB::statement("ALTER TABLE TrBeliBBHd ALTER COLUMN TTLTax NUMERIC(18, 6) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE TrBeliBBDt ALTER COLUMN Harga NUMERIC(19, 4) NULL");
        DB::statement("ALTER TABLE TrBeliBBDt ALTER COLUMN Jml NUMERIC(18, 4) NULL");

        DB::statement("ALTER TABLE TrBeliBBHd ALTER COLUMN DiscAmount NUMERIC(18, 4) NULL");
        DB::statement("ALTER TABLE TrBeliBBHd ALTER COLUMN interest NUMERIC(18, 4) NULL");
        DB::statement("ALTER TABLE TrBeliBBHd ALTER COLUMN STPb NUMERIC(18, 4) NULL");
        DB::statement("ALTER TABLE TrBeliBBHd ALTER COLUMN TTLPb NUMERIC(18, 4) NULL");
        DB::statement("ALTER TABLE TrBeliBBHd ALTER COLUMN TTLTax NUMERIC(18, 4) NULL");
    }
};
