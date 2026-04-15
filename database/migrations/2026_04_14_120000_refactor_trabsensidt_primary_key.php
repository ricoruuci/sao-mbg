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
        $pk = DB::selectOne(
            "SELECT kc.name AS pk_name
             FROM sys.key_constraints kc
             INNER JOIN sys.tables t ON kc.parent_object_id = t.object_id
             WHERE kc.type = 'PK' AND t.name = 'trabsensidt'"
        );

        if ($pk && !empty($pk->pk_name)) {
            DB::statement('ALTER TABLE trabsensidt DROP CONSTRAINT [' . $pk->pk_name . ']');
        }

        $rowIdExists = DB::selectOne(
            "SELECT 1 AS exists_flag
             FROM sys.columns
             WHERE object_id = OBJECT_ID('trabsensidt')
               AND name = 'tr_absensi_dt_row_id'"
        );

        if (!$rowIdExists) {
            DB::statement('ALTER TABLE trabsensidt ADD tr_absensi_dt_row_id BIGINT IDENTITY(1,1) NOT NULL');
        }

        DB::statement('ALTER TABLE trabsensidt ADD CONSTRAINT PK_trabsensidt_row_id PRIMARY KEY (tr_absensi_dt_row_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $pk = DB::selectOne(
            "SELECT kc.name AS pk_name
             FROM sys.key_constraints kc
             INNER JOIN sys.tables t ON kc.parent_object_id = t.object_id
             WHERE kc.type = 'PK' AND t.name = 'trabsensidt'"
        );

        if ($pk && !empty($pk->pk_name)) {
            DB::statement('ALTER TABLE trabsensidt DROP CONSTRAINT [' . $pk->pk_name . ']');
        }

        $rowIdExists = DB::selectOne(
            "SELECT 1 AS exists_flag
             FROM sys.columns
             WHERE object_id = OBJECT_ID('trabsensidt')
               AND name = 'tr_absensi_dt_row_id'"
        );

        if ($rowIdExists) {
            DB::statement('ALTER TABLE trabsensidt DROP COLUMN tr_absensi_dt_row_id');
        }

        DB::statement('ALTER TABLE trabsensidt ADD CONSTRAINT PK_trabsensidt_tr_absensi_dt_id PRIMARY KEY (tr_absensi_dt_id)');
    }
};
