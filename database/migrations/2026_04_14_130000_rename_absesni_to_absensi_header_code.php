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
        $this->dropDetailToHeaderForeignKey();

        if ($this->columnExists('trabsensihd', 'tr_absesni_header_code') && !$this->columnExists('trabsensihd', 'tr_absensi_header_code')) {
            DB::statement("EXEC sp_rename 'trabsensihd.tr_absesni_header_code', 'tr_absensi_header_code', 'COLUMN'");
        }

        if ($this->columnExists('trabsensidt', 'tr_absesni_header_code') && !$this->columnExists('trabsensidt', 'tr_absensi_header_code')) {
            DB::statement("EXEC sp_rename 'trabsensidt.tr_absesni_header_code', 'tr_absensi_header_code', 'COLUMN'");
        }

        if ($this->columnExists('trabsensidt', 'tr_absensi_header_code') && $this->columnExists('trabsensihd', 'tr_absensi_header_code')) {
            DB::statement('ALTER TABLE trabsensidt ADD CONSTRAINT FK_trabsensidt_trabsensihd_absensi FOREIGN KEY (tr_absensi_header_code) REFERENCES trabsensihd(tr_absensi_header_code)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropDetailToHeaderForeignKey();

        if ($this->columnExists('trabsensidt', 'tr_absensi_header_code') && !$this->columnExists('trabsensidt', 'tr_absesni_header_code')) {
            DB::statement("EXEC sp_rename 'trabsensidt.tr_absensi_header_code', 'tr_absesni_header_code', 'COLUMN'");
        }

        if ($this->columnExists('trabsensihd', 'tr_absensi_header_code') && !$this->columnExists('trabsensihd', 'tr_absesni_header_code')) {
            DB::statement("EXEC sp_rename 'trabsensihd.tr_absensi_header_code', 'tr_absesni_header_code', 'COLUMN'");
        }

        if ($this->columnExists('trabsensidt', 'tr_absesni_header_code') && $this->columnExists('trabsensihd', 'tr_absesni_header_code')) {
            DB::statement('ALTER TABLE trabsensidt ADD CONSTRAINT FK_trabsensidt_trabsensihd_absesni FOREIGN KEY (tr_absesni_header_code) REFERENCES trabsensihd(tr_absesni_header_code)');
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        $result = DB::selectOne(
            "SELECT 1 AS exists_flag
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_NAME = ? AND COLUMN_NAME = ?",
            [$table, $column]
        );

        return $result !== null;
    }

    private function dropDetailToHeaderForeignKey(): void
    {
        $foreignKeys = DB::select(
            "SELECT fk.name
             FROM sys.foreign_keys fk
             INNER JOIN sys.tables parent_t ON fk.parent_object_id = parent_t.object_id
             INNER JOIN sys.tables ref_t ON fk.referenced_object_id = ref_t.object_id
             WHERE parent_t.name = 'trabsensidt' AND ref_t.name = 'trabsensihd'"
        );

        foreach ($foreignKeys as $fk) {
            DB::statement('ALTER TABLE trabsensidt DROP CONSTRAINT [' . $fk->name . ']');
        }
    }
};
