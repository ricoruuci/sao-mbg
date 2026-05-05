<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

class TrSpmHd extends BaseModel
{
    use HasFactory;

    protected $table = 'trspm_hd';

    public $timestamps = false;

    public function insertData($param)
    {
        $result = DB::insert(
            "INSERT INTO trspm_hd
            (trspm_hd_code, trspm_hd_date, trspm_hd_date_from, trspm_hd_date_to, trspm_hd_company_id, trspm_hd_company_name, trspm_hd_subtotal, trspm_hd_subbonuses, trspm_hd_note, trspm_hd_work_days, trspm_hd_overtime_adjustment, upddate, upduser)
            VALUES
            (:trspm_hd_code, :trspm_hd_date, :trspm_hd_date_from, :trspm_hd_date_to, :trspm_hd_company_id, :trspm_hd_company_name, :trspm_hd_subtotal, :trspm_hd_subbonuses, :trspm_hd_note, :trspm_hd_work_days, :trspm_hd_overtime_adjustment, getDate(), :upduser)",
            [
                'trspm_hd_code' => $param['trspm_hd_code'],
                'trspm_hd_date' => $param['trspm_hd_date'],
                'trspm_hd_date_from' => $param['trspm_hd_date_from'],
                'trspm_hd_date_to' => $param['trspm_hd_date_to'],
                'trspm_hd_company_id' => $param['trspm_hd_company_id'],
                'trspm_hd_company_name' => $param['trspm_hd_company_name'],
                'trspm_hd_subtotal' => $param['trspm_hd_subtotal'] ?? 0,
                'trspm_hd_subbonuses' => $param['trspm_hd_subbonuses'] ?? 0,
                'trspm_hd_note' => $param['trspm_hd_note'] ?? '',
                'trspm_hd_work_days' => $param['trspm_hd_work_days'] ?? 0,
                'trspm_hd_overtime_adjustment' => $param['trspm_hd_overtime_adjustment'] ?? 0,
                'upduser' => $param['upduser']
            ]
        );

        return $result;
    }

    public function getAllData($param)
    {
        $result = DB::select(
            "SELECT
            id,
            trspm_hd_code,
            trspm_hd_date,
            trspm_hd_date_from,
            trspm_hd_date_to,
            trspm_hd_company_id,
            trspm_hd_company_name,
            trspm_hd_subtotal,
            trspm_hd_subbonuses,
            (trspm_hd_subtotal + trspm_hd_subbonuses) as trspm_hd_grandtotal,
            trspm_hd_note,
            trspm_hd_work_days,
            trspm_hd_overtime_adjustment,
            upddate,
            upduser
            FROM trspm_hd
            WHERE convert(varchar(10), trspm_hd_date, 112) BETWEEN :dari AND :sampai
            AND ISNULL(trspm_hd_code, '') LIKE :trspm_hd_code_keyword
            ORDER BY trspm_hd_date ASC",
            [
                'dari' => $param['dari'],
                'sampai' => $param['sampai'],
                'trspm_hd_code_keyword' => '%' . $param['trspm_hd_code_keyword'] . '%'
            ]
        );

        return $result;
    }

    public function getDataById($code)
    {
        $result = DB::selectOne(
            "SELECT
            id,
            trspm_hd_code,
            trspm_hd_date,
            trspm_hd_date_from,
            trspm_hd_date_to,
            trspm_hd_company_id,
            trspm_hd_company_name,
            trspm_hd_subtotal,
            trspm_hd_subbonuses,
            (trspm_hd_subtotal + trspm_hd_subbonuses) as trspm_hd_grandtotal,
            trspm_hd_note,
            trspm_hd_work_days,
            trspm_hd_overtime_adjustment,
            upddate,
            upduser
            FROM trspm_hd
            WHERE trspm_hd_code = :code",
            [
                'code' => $code
            ]
        );

        return $result;
    }

    public function updateData($param)
    {
        $result = DB::update(
            "UPDATE trspm_hd SET
            trspm_hd_date = :trspm_hd_date,
            trspm_hd_date_from = :trspm_hd_date_from,
            trspm_hd_date_to = :trspm_hd_date_to,
            trspm_hd_company_id = :trspm_hd_company_id,
            trspm_hd_company_name = :trspm_hd_company_name,
            trspm_hd_subtotal = :trspm_hd_subtotal,
            trspm_hd_subbonuses = :trspm_hd_subbonuses,
            trspm_hd_note = :trspm_hd_note,
            trspm_hd_work_days = :trspm_hd_work_days,
            trspm_hd_overtime_adjustment = :trspm_hd_overtime_adjustment,
            upddate = getDate(),
            upduser = :upduser
            WHERE trspm_hd_code = :trspm_hd_code",
            [
                'trspm_hd_code' => $param['trspm_hd_code'],
                'trspm_hd_date' => $param['trspm_hd_date'],
                'trspm_hd_date_from' => $param['trspm_hd_date_from'],
                'trspm_hd_date_to' => $param['trspm_hd_date_to'],
                'trspm_hd_company_id' => $param['trspm_hd_company_id'],
                'trspm_hd_company_name' => $param['trspm_hd_company_name'],
                'trspm_hd_subtotal' => $param['trspm_hd_subtotal'] ?? 0,
                'trspm_hd_subbonuses' => $param['trspm_hd_subbonuses'] ?? 0,
                'trspm_hd_note' => $param['trspm_hd_note'] ?? '',
                'trspm_hd_work_days' => $param['trspm_hd_work_days'] ?? 0,
                'trspm_hd_overtime_adjustment' => $param['trspm_hd_overtime_adjustment'] ?? 0,
                'upduser' => $param['upduser']
            ]
        );

        return $result;
    }

    public function deleteData($id)
    {
        $result = DB::delete(
            'DELETE FROM trspm_hd WHERE id = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function deleteDataByCode($code)
    {
        $result = DB::delete(
            'DELETE FROM trspm_hd WHERE trspm_hd_code = :code',
            [
                'code' => $code
            ]
        );

        return $result;
    }

    public function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT id, trspm_hd_code FROM trspm_hd WHERE id = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function cekCode($code)
    {
        $result = DB::selectOne(
            'SELECT id, trspm_hd_code FROM trspm_hd WHERE trspm_hd_code = :code',
            [
                'code' => $code
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($company_id, $date_period)
    {
        $autoNumber = $this->autoNumber($this->table, 'trspm_hd_code', 'SPM' . $company_id . date('Ym', strtotime($date_period)), '000');

        return $autoNumber;
    }

    public function hitungTotal($id)
    {
        $result = DB::selectOne(
            "SELECT ISNULL(SUM(trspm_dt_total), 0) as total
            FROM trspm_dt
            WHERE trspm_hd_id = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function updateTotal($params)
    {
        $result = DB::update(
            "UPDATE trspm_hd
            SET trspm_hd_subtotal = :total
            WHERE id = :id",
            [
                'total' => $params['total'],
                'id' => $params['id']
            ]
        );

        return $result;
    }

    public function getCompanyName($company_id)
    {
        $result = DB::selectOne(
            "SELECT ISNULL(company_name, '') as company_name
            FROM mscabang
            WHERE company_id = :company_id",
            [
                'company_id' => $company_id
            ]
        );

        return $result ? $result->company_name : '';
    }
}
