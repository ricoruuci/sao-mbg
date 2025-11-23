<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

use function PHPUnit\Framework\isNull;

class PayrollHd extends BaseModel //nama class
{
    use HasFactory;

    protected $table = 'payrollhd'; //sesuaiin nama tabel

    public $timestamps = false;

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO payrollhd
            (tx_id, tx_date, tx_period, upddate, upduser, work_days, total,company_id)
            VALUES
            (:tx_id, :tx_date, :tx_period, getDate(), :upduser, :work_days, 0, :company_id)",
            [
                'tx_id' => $param['tx_id'],
                'tx_date' => $param['tx_date'],
                'tx_period' => $param['tx_period'],
                'upduser' => $param['upduser'],
                'work_days' => $param['work_days'],
                'company_id' => $param['company_id']
            ]
        );

        return $result;
    }

    function getAllData($param)
    {

        $result = DB::select(
           "SELECT
            a.id,a.tx_id,a.tx_date,a.tx_period,a.work_days,a.upddate,a.upduser,a.total,
            a.company_id,c.company_code,c.company_name,c.company_address
            from payrollhd a
            left join mscabang c on a.company_id = c.company_id
			where
			convert(varchar(10),a.tx_date,112) between :dari and :sampai
            and isnull(a.tx_id,'') like :tx_id_keyword and isnull(a.tx_period,'') like :tx_period_keyword
            and a.company_id =:company_id
            order by a.tx_date ASC",
            [
                'dari' => $param['dari'],
                'sampai' => $param['sampai'],
                'tx_id_keyword' => '%' . $param['tx_id_keyword'] . '%',
                'tx_period_keyword' => '%' . $param['tx_period_keyword'] . '%',
                'company_id' => $param['company_id']
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT
            a.id,a.tx_id,a.tx_date,a.tx_period,a.work_days,a.upddate,a.upduser,a.total,
            a.company_id,c.company_code,c.company_name,c.company_address
            from payrollhd a
            left join mscabang c on a.company_id = c.company_id
			WHERE a.id = :id ",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function updateData($param)
    {
        $result = DB::update(
            'UPDATE payrollhd SET
                tx_date = :tx_date,
                tx_period = :tx_period,
                work_days = :work_days,
                upddate = getDate(),
                upduser = :upduser
            WHERE id = :id ',
            [
                'id' => $param['id'],
                'tx_date' => $param['tx_date'],
                'tx_period' => $param['tx_period'],
                'work_days' => $param['work_days'],
                'upduser' => $param['upduser'],
            ]
        );

        return $result;
    }


    function deleteData($id)
    {

        $result = DB::delete(
            'DELETE FROM payrollhd WHERE id = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT id,company_id,tx_id from payrollhd WHERE id = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekId($tx_id)
    {
        $result = DB::selectOne(
            'SELECT id from payrollhd WHERE tx_id = :tx_id',
            [
                'tx_id' => $tx_id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($company_id,$tx_period)
    {

        $autoNumber = $this->autoNumber($this->table, 'tx_id','PAY'.$company_id.$tx_period, '000');

        return $autoNumber;
    }

    function hitungTotal($id)
    {
        $result = DB::selectOne(
            "SELECT isnull(sum(a.total_amount),0) as total
            from payrolldt a
            where a.id=:id ",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function updateTotal($params)
    {
        $result = DB::update(
            "UPDATE payrollhd
            SET
            total = :total
            where id=:id ",
            [
                'total' => $params['total'],
                'id' => $params['id']
            ]
        );

        return $result;
    }
}
