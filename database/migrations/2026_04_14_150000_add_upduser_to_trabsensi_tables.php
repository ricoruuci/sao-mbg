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
        $this->addUpduserIfMissing('trabsensihd');
        $this->addUpduserIfMissing('trabsensidt');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropUpduserIfExists('trabsensidt');
        $this->dropUpduserIfExists('trabsensihd');
    }

    private function addUpduserIfMissing(string $table): void
    {
        $exists = DB::selectOne(
            "SELECT 1 AS exists_flag
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_NAME = ?
               AND COLUMN_NAME = 'upduser'",
            [$table]
        );

        if (!$exists) {
            DB::statement("ALTER TABLE {$table} ADD upduser NVARCHAR(100) NULL");
        }
    }

    private function dropUpduserIfExists(string $table): void
    {
        $exists = DB::selectOne(
            "SELECT 1 AS exists_flag
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_NAME = ?
               AND COLUMN_NAME = 'upduser'",
            [$table]
        );

        if ($exists) {
            DB::statement("ALTER TABLE {$table} DROP COLUMN upduser");
        }
    }
};
