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
        DB::statement("ALTER TABLE TrBeliBBHd ALTER COLUMN tax DECIMAL(9, 6) NULL");

        DB::statement("\n            ;WITH calc AS (\n                SELECT\n                    h.nota,\n                    h.kdsupplier,\n                    CAST(ISNULL(SUM(CAST(d.jml AS DECIMAL(28, 6)) * CAST(d.harga AS DECIMAL(28, 6))), 0) AS DECIMAL(28, 6)) AS subtotal,\n                    CAST(ISNULL(h.discamount, 0) AS DECIMAL(28, 6)) AS discamount,\n                    CAST(ISNULL(h.tax, 0) AS DECIMAL(18, 6)) AS tax\n                FROM TrBeliBBHd h\n                LEFT JOIN TrBeliBBDt d ON d.nota = h.nota AND d.kdsupplier = h.kdsupplier\n                GROUP BY h.nota, h.kdsupplier, h.discamount, h.tax\n            )\n            UPDATE h\n            SET\n                h.stpb = calc.subtotal,\n                h.ttltax = (calc.subtotal - calc.discamount) * calc.tax * 0.01,\n                h.ttlpb = (calc.subtotal - calc.discamount) * (1 + (calc.tax * 0.01))\n            FROM TrBeliBBHd h\n            INNER JOIN calc ON calc.nota = h.nota AND calc.kdsupplier = h.kdsupplier\n        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE TrBeliBBHd ALTER COLUMN tax REAL NULL");
    }
};
