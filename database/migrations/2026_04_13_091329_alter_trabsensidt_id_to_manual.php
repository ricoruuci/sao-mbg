<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        DB::statement('ALTER TABLE trabsensidt DROP COLUMN tr_absensi_dt_id');
        DB::statement('ALTER TABLE trabsensidt ADD tr_absensi_dt_id NVARCHAR(255) PRIMARY KEY');
    }

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

        DB::statement('ALTER TABLE trabsensidt DROP COLUMN tr_absensi_dt_id');
        DB::statement('ALTER TABLE trabsensidt ADD tr_absensi_dt_id INT IDENTITY(1,1) PRIMARY KEY');
    }
};
