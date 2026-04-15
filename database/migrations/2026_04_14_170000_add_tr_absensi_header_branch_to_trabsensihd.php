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
        $exists = DB::selectOne(
            "SELECT 1 AS exists_flag
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_NAME = 'trabsensihd'
               AND COLUMN_NAME = 'tr_absensi_header_branch'"
        );

        if (!$exists) {
            DB::statement("ALTER TABLE trabsensihd ADD tr_absensi_header_branch NVARCHAR(255) NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $exists = DB::selectOne(
            "SELECT 1 AS exists_flag
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_NAME = 'trabsensihd'
               AND COLUMN_NAME = 'tr_absensi_header_branch'"
        );

        if ($exists) {
            DB::statement("ALTER TABLE trabsensihd DROP COLUMN tr_absensi_header_branch");
        }
    }
};
