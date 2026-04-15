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
        $columnExists = DB::selectOne(
            "SELECT 1 AS exists_flag
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_NAME = 'trabsensidt'
               AND COLUMN_NAME = 'tr_absensi_dt_nominal'"
        );

        if (!$columnExists) {
            DB::statement('ALTER TABLE trabsensidt ADD tr_absensi_dt_nominal DECIMAL(18,2) NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columnExists = DB::selectOne(
            "SELECT 1 AS exists_flag
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_NAME = 'trabsensidt'
               AND COLUMN_NAME = 'tr_absensi_dt_nominal'"
        );

        if ($columnExists) {
            DB::statement('ALTER TABLE trabsensidt DROP COLUMN tr_absensi_dt_nominal');
        }
    }
};
