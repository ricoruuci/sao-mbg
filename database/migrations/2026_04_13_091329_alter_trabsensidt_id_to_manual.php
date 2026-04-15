<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE trabsensidt DROP CONSTRAINT PK__trabsens__C42DC4E809E8365A');
        DB::statement('ALTER TABLE trabsensidt DROP COLUMN tr_absensi_dt_id');
        DB::statement('ALTER TABLE trabsensidt ADD tr_absensi_dt_id NVARCHAR(255) PRIMARY KEY');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE trabsensidt DROP CONSTRAINT PK_trabsensidt_tr_absensi_dt_id');
        DB::statement('ALTER TABLE trabsensidt DROP COLUMN tr_absensi_dt_id');
        DB::statement('ALTER TABLE trabsensidt ADD tr_absensi_dt_id INT IDENTITY(1,1) PRIMARY KEY');
    }
};
