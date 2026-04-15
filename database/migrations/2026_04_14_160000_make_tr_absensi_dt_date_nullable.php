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
        DB::statement('ALTER TABLE trabsensidt ALTER COLUMN tr_absensi_dt_date DATE NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE trabsensidt ALTER COLUMN tr_absensi_dt_date DATE NOT NULL');
    }
};
