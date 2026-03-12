<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class InvoiceHd extends BaseModel
{
    use HasFactory;

    protected $table = 'trinvoicehd';

    public $timestamps = false;

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT trinvoicehd (invoice_code,invoice_date,invoice_to,invoice_note,invoice_subtotal,invoice_ppn,invoice_ppn_amount,invoice_ppn_flag,upddate,upduser,company_id)
            VALUES (?, ?, ?, ?, 0, ?, 0, ?, getdate(), ?, ?)",
            [
                $params['invoice_code'],
                $params['invoice_date'],
                $params['invoice_to'],
                $params['invoice_note'],
                $params['invoice_ppn'],
                $params['invoice_ppn_flag'],
                $params['upduser'],
                $params['company_id']
            ]
        );

        return $result;
    }

    function updateData($params)
    {
        $result = DB::update(
            "UPDATE trinvoicehd
            SET
            invoice_date = ?,
            invoice_to = ?,
            invoice_note = ?,
            invoice_ppn = ?,
            invoice_ppn_flag = ?,
            upddate = getdate(),
            upduser = ?
            WHERE invoice_code = ?",
            [
                $params['invoice_date'],
                $params['invoice_to'],
                $params['invoice_note'],
                $params['invoice_ppn'],
                $params['invoice_ppn_flag'],
                $params['upduser'],
                $params['invoice_code']
            ]
        );

        return $result;
    }

    function getAllData($params)
    {
        $addCon = ''; // default kosong

        if (!empty($params['company_id']))
        {
            $addCon = 'and a.company_id = ? ';
            $binding = [
                $params['dari'],
                $params['sampai'],
                $params['company_id'],
                '%'.$params['search_keyword'].'%',
                '%'.$params['search_keyword'].'%'
            ];
        }
        else
        {
            $binding = [
                $params['dari'],
                $params['sampai'],
                '%'.$params['search_keyword'].'%',
                '%'.$params['search_keyword'].'%'
            ];
        }

        $result = DB::select(
            "SELECT a.invoice_code,a.invoice_date,a.invoice_to,a.invoice_note,
            a.invoice_subtotal,a.invoice_ppn,a.invoice_ppn_amount,a.invoice_ppn_flag,
            a.invoice_subtotal + a.invoice_ppn_amount as invoice_total,
            a.upddate,a.upduser,
            a.company_id,c.company_code,c.company_name,c.company_address
            from trinvoicehd a
            left join mscabang c on a.company_id = c.company_id
            where convert(varchar(10),a.invoice_date,112) between ? and ?
            $addCon
            and (ISNULL(a.invoice_code,'') like ? or ISNULL(a.invoice_to,'') like ?)
            order by a.invoice_code desc",
            $binding
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT a.invoice_code,a.invoice_date,a.invoice_to,a.invoice_note,
            a.invoice_subtotal,a.invoice_ppn,a.invoice_ppn_amount,a.invoice_ppn_flag,
            a.invoice_subtotal + a.invoice_ppn_amount as invoice_total,
            a.upddate,a.upduser,
            a.company_id,c.company_code,c.company_name,c.company_address
            from trinvoicehd a
            left join mscabang c on a.company_id = c.company_id
            where a.invoice_code = ?",
            [
                $id
            ]
        );

        return $result;
    }

    function hitungTotal($id)
    {
        $result = DB::selectOne(
            "SELECT 
                k.invoice_code,
                k.total as invoice_subtotal,
                case when k.invoice_ppn_flag = 1 then k.total * k.invoice_ppn * 0.01 else 0 end as invoice_ppn_amount,
                k.total + (case when k.invoice_ppn_flag = 1 then k.total * k.invoice_ppn * 0.01 else 0 end) as invoice_total 
            from (
                select 
                    a.invoice_code,
                    isnull(sum(a.invoice_detail_qty * a.invoice_detail_price),0) as total,
                    b.invoice_ppn,
                    b.invoice_ppn_flag
                from trinvoicedt a 
                inner join trinvoicehd b on a.invoice_code = b.invoice_code
                where a.invoice_code = ?
                group by a.invoice_code, b.invoice_ppn, b.invoice_ppn_flag
            ) as k",
            [
                $id
            ]
        );

        return $result;
    }

    function updateTotal($params)
    {
        $ppn_amount = $params['invoice_ppn_amount'] ?? 0;
        
        $result = DB::update(
            "UPDATE trinvoicehd
            SET
            invoice_subtotal = ?,
            invoice_ppn_amount = ?
            where invoice_code = ?",
            [
                $params['invoice_subtotal'],
                $ppn_amount,
                $params['invoice_code']
            ]
        );

        return $result;
    }

    function cekData($id)
    {
        $result = DB::selectOne(
            "SELECT invoice_code,company_id from trinvoicehd WHERE invoice_code = ?",
            [
                $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($invoice_date,$company_code = 'MBG')
    {
        $tahunBulan = '/' . $company_code . '/' . substr($invoice_date, 2, 2) . '/' . substr($invoice_date, 4, 2) . '/';
        
        $autoNumber = $this->autoNumber($this->table, 'invoice_code', 'INV'.$tahunBulan, '000');

        return $autoNumber;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM trinvoicehd where invoice_code = ?",
            [
                $id
            ]
        );

        return $result;
    }
}

?>
