<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class InvoiceDt extends BaseModel
{
    use HasFactory;

    protected $table = 'trinvoicedt';

    public $timestamps = false;

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO trinvoicedt
            (invoice_code, invoice_detail_description, invoice_detail_qty, invoice_detail_price, invoice_detail_total, upddate, upduser)
            VALUES
            (?, ?, ?, ?, ?, getdate(), ?)",
            [
                $params['invoice_code'],
                $params['invoice_detail_description'],
                $params['invoice_detail_qty'],
                $params['invoice_detail_price'],
                $params['invoice_detail_total'],
                $params['upduser']
            ]
        );

        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM trinvoicedt where invoice_code = ?",
            [
                $id
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::select(
            "SELECT
            a.invoice_code,
            a.invoice_detail_description,
            a.invoice_detail_qty,
            a.invoice_detail_price,
            a.invoice_detail_total,
            a.upddate,
            a.upduser
            from trinvoicedt a
            where a.invoice_code = ?",
            [
                $id
            ]
        );

        return $result;
    }
}

?>
