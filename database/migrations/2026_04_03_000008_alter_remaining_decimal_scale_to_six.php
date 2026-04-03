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
        foreach ($this->newSpecs() as $spec) {
            $this->alterColumn(...$spec);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->oldSpecs() as $spec) {
            $this->alterColumn(...$spec);
        }
    }

    private function alterColumn(
        string $table,
        string $column,
        string $type,
        int $precision,
        int $scale,
        bool $nullable
    ): void {
        $nullableSql = $nullable ? 'NULL' : 'NOT NULL';

        DB::statement(sprintf(
            'ALTER TABLE [%s] ALTER COLUMN [%s] %s(%d, %d) %s',
            $table,
            $column,
            strtoupper($type),
            $precision,
            $scale,
            $nullableSql
        ));
    }

    private function newSpecs(): array
    {
        return [
            ['AllBB', 'harga', 'numeric', 18, 6, true],
            ['AllItem', 'HPP', 'numeric', 18, 6, true],
            ['AllItem', 'Price', 'numeric', 18, 6, true],
            ['AllItem', 'Qty', 'numeric', 18, 6, true],
            ['CFTrKKBBDt', 'Amount', 'numeric', 18, 6, true],
            ['CFTrKKBBHd', 'Total', 'numeric', 18, 6, true],
            ['dBeli', 'harga', 'numeric', 16, 6, true],
            ['MsBahanBaku', 'HargaBeli', 'numeric', 18, 6, true],
            ['MsCustomer', 'customer_limit_piutang', 'decimal', 18, 6, false],
            ['MsCustomer', 'Disc', 'numeric', 18, 6, true],
            ['MsKaryawan', 'meal_amount', 'numeric', 18, 6, true],
            ['MsKaryawan', 'other_amount', 'numeric', 18, 6, true],
            ['MsKaryawan', 'salary_amount', 'numeric', 18, 6, true],
            ['MsMenuHd', 'Harga', 'numeric', 18, 6, true],
            ['MsMenuHd', 'HrgQ', 'numeric', 19, 6, true],
            ['MsMenuHd', 'Point', 'numeric', 18, 6, true],
            ['MsPattyCash', 'PattyCash', 'numeric', 19, 6, true],
            ['PayrollDt', 'meal_amount', 'numeric', 18, 6, true],
            ['PayrollDt', 'other_amount', 'numeric', 18, 6, true],
            ['PayrollDt', 'salary_amount', 'numeric', 18, 6, true],
            ['PayrollDt', 'total_amount', 'numeric', 18, 6, true],
            ['PayrollHd', 'total', 'numeric', 18, 6, true],
            ['TmpHPP', 'HPP', 'numeric', 19, 6, true],
            ['TmpRptCostSales', 'HPP', 'numeric', 19, 6, true],
            ['TmpRptHPPStok', 'HPP', 'numeric', 19, 6, true],
            ['trinvoicedt', 'invoice_detail_price', 'decimal', 15, 6, false],
            ['trinvoicedt', 'invoice_detail_qty', 'decimal', 10, 6, false],
            ['trinvoicedt', 'invoice_detail_total', 'decimal', 15, 6, false],
            ['trinvoicehd', 'invoice_ppn', 'decimal', 9, 6, false],
            ['trinvoicehd', 'invoice_ppn_amount', 'decimal', 15, 6, false],
            ['trinvoicehd', 'invoice_subtotal', 'decimal', 15, 6, false],
            ['TrIssued', 'Qty', 'numeric', 18, 6, true],
            ['TrJualDt', 'Harga', 'numeric', 19, 6, true],
            ['TrJualDt', 'Jumlah1', 'numeric', 18, 6, true],
            ['TrJualDt', 'Point', 'numeric', 18, 6, true],
            ['TrJualDt', 'Selisih', 'numeric', 18, 6, true],
            ['TrJualHd', 'STPj', 'numeric', 18, 6, true],
            ['TrJualHd', 'TTLBiaya', 'numeric', 18, 6, true],
            ['TrJualHd', 'TTLDisc', 'numeric', 18, 6, true],
            ['TrJualHd', 'TTLMakanan', 'numeric', 18, 6, true],
            ['TrJualHd', 'TTLMinuman', 'numeric', 18, 6, true],
            ['TrJualHd', 'TTLPaket', 'numeric', 18, 6, true],
            ['TrJualHd', 'TTLPj', 'numeric', 18, 6, true],
            ['TrJualHd', 'TTLTax', 'numeric', 18, 6, true],
        ];
    }

    private function oldSpecs(): array
    {
        return [
            ['AllBB', 'harga', 'numeric', 18, 4, true],
            ['AllItem', 'HPP', 'numeric', 18, 4, true],
            ['AllItem', 'Price', 'numeric', 18, 4, true],
            ['AllItem', 'Qty', 'numeric', 18, 4, true],
            ['CFTrKKBBDt', 'Amount', 'numeric', 18, 4, true],
            ['CFTrKKBBHd', 'Total', 'numeric', 18, 4, true],
            ['dBeli', 'harga', 'numeric', 10, 0, true],
            ['MsBahanBaku', 'HargaBeli', 'numeric', 18, 4, true],
            ['MsCustomer', 'customer_limit_piutang', 'decimal', 18, 2, false],
            ['MsCustomer', 'Disc', 'numeric', 18, 4, true],
            ['MsKaryawan', 'meal_amount', 'numeric', 18, 4, true],
            ['MsKaryawan', 'other_amount', 'numeric', 18, 4, true],
            ['MsKaryawan', 'salary_amount', 'numeric', 18, 4, true],
            ['MsMenuHd', 'Harga', 'numeric', 18, 4, true],
            ['MsMenuHd', 'HrgQ', 'numeric', 19, 4, true],
            ['MsMenuHd', 'Point', 'numeric', 18, 4, true],
            ['MsPattyCash', 'PattyCash', 'numeric', 19, 4, true],
            ['PayrollDt', 'meal_amount', 'numeric', 18, 4, true],
            ['PayrollDt', 'other_amount', 'numeric', 18, 4, true],
            ['PayrollDt', 'salary_amount', 'numeric', 18, 4, true],
            ['PayrollDt', 'total_amount', 'numeric', 18, 4, true],
            ['PayrollHd', 'total', 'numeric', 18, 4, true],
            ['TmpHPP', 'HPP', 'numeric', 19, 4, true],
            ['TmpRptCostSales', 'HPP', 'numeric', 19, 4, true],
            ['TmpRptHPPStok', 'HPP', 'numeric', 19, 4, true],
            ['trinvoicedt', 'invoice_detail_price', 'decimal', 15, 2, false],
            ['trinvoicedt', 'invoice_detail_qty', 'decimal', 10, 2, false],
            ['trinvoicedt', 'invoice_detail_total', 'decimal', 15, 2, false],
            ['trinvoicehd', 'invoice_ppn', 'decimal', 5, 2, false],
            ['trinvoicehd', 'invoice_ppn_amount', 'decimal', 15, 2, false],
            ['trinvoicehd', 'invoice_subtotal', 'decimal', 15, 2, false],
            ['TrIssued', 'Qty', 'numeric', 18, 4, true],
            ['TrJualDt', 'Harga', 'numeric', 19, 4, true],
            ['TrJualDt', 'Jumlah1', 'numeric', 18, 4, true],
            ['TrJualDt', 'Point', 'numeric', 18, 4, true],
            ['TrJualDt', 'Selisih', 'numeric', 18, 4, true],
            ['TrJualHd', 'STPj', 'numeric', 18, 4, true],
            ['TrJualHd', 'TTLBiaya', 'numeric', 18, 4, true],
            ['TrJualHd', 'TTLDisc', 'numeric', 18, 4, true],
            ['TrJualHd', 'TTLMakanan', 'numeric', 18, 4, true],
            ['TrJualHd', 'TTLMinuman', 'numeric', 18, 4, true],
            ['TrJualHd', 'TTLPaket', 'numeric', 18, 4, true],
            ['TrJualHd', 'TTLPj', 'numeric', 18, 4, true],
            ['TrJualHd', 'TTLTax', 'numeric', 18, 4, true],
        ];
    }
};
