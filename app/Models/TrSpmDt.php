<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

class TrSpmDt extends BaseModel
{
    use HasFactory;

    protected $table = 'trspm_dt';

    public $timestamps = false;

    public function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO trspm_dt
            (trspm_hd_id, trspm_dt_user_code, trspm_dt_user_name, trspm_dt_divisi, trspm_dt_work_day, trspm_dt_price, trspm_dt_bonuses, trspm_dt_overtime, trspm_dt_total, upddate, upduser)
            VALUES
            (:trspm_hd_id, :trspm_dt_user_code, :trspm_dt_user_name, :trspm_dt_divisi, :trspm_dt_work_day, :trspm_dt_price, :trspm_dt_bonuses, :trspm_dt_overtime, :trspm_dt_total, getDate(), :upduser)",
            [
                'trspm_hd_id' => $params['trspm_hd_id'],
                'trspm_dt_user_code' => $params['trspm_dt_user_code'],
                'trspm_dt_user_name' => $params['trspm_dt_user_name'],
                'trspm_dt_divisi' => $params['trspm_dt_divisi'] ?? '',
                'trspm_dt_work_day' => $params['trspm_dt_work_day'] ?? 0,
                'trspm_dt_price' => $params['trspm_dt_price'] ?? 0,
                'trspm_dt_bonuses' => $params['trspm_dt_bonuses'] ?? 0,
                'trspm_dt_overtime' => $params['trspm_dt_overtime'] ?? 0,
                'trspm_dt_total' => $params['trspm_dt_total'] ?? 0,
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    public function deleteData($trspm_hd_id)
    {
        $result = DB::delete(
            "DELETE FROM trspm_dt WHERE trspm_hd_id = :trspm_hd_id",
            [
                'trspm_hd_id' => $trspm_hd_id
            ]
        );

        return $result;
    }

    public function getDataById($trspm_hd_id)
    {
        $result = DB::select(
            "SELECT
            id,
            trspm_hd_id,
            trspm_dt_user_code,
            trspm_dt_user_name,
            trspm_dt_divisi,
            trspm_dt_work_day,
            trspm_dt_price,
            trspm_dt_bonuses,
            trspm_dt_overtime,
            trspm_dt_total,
            upddate,
            upduser
            FROM trspm_dt
            WHERE trspm_hd_id = :trspm_hd_id
            ORDER BY trspm_dt_user_code ASC",
            [
                'trspm_hd_id' => $trspm_hd_id
            ]
        );

        return $result;
    }

    public function updateData($params)
    {
        $result = DB::update(
            "UPDATE trspm_dt SET
            trspm_dt_user_code = :trspm_dt_user_code,
            trspm_dt_user_name = :trspm_dt_user_name,
            trspm_dt_divisi = :trspm_dt_divisi,
            trspm_dt_work_day = :trspm_dt_work_day,
            trspm_dt_price = :trspm_dt_price,
            trspm_dt_bonuses = :trspm_dt_bonuses,
            trspm_dt_overtime = :trspm_dt_overtime,
            trspm_dt_total = :trspm_dt_total,
            upddate = getDate(),
            upduser = :upduser
            WHERE id = :id",
            [
                'id' => $params['id'],
                'trspm_dt_user_code' => $params['trspm_dt_user_code'],
                'trspm_dt_user_name' => $params['trspm_dt_user_name'],
                'trspm_dt_divisi' => $params['trspm_dt_divisi'] ?? '',
                'trspm_dt_work_day' => $params['trspm_dt_work_day'] ?? 0,
                'trspm_dt_price' => $params['trspm_dt_price'] ?? 0,
                'trspm_dt_bonuses' => $params['trspm_dt_bonuses'] ?? 0,
                'trspm_dt_overtime' => $params['trspm_dt_overtime'] ?? 0,
                'trspm_dt_total' => $params['trspm_dt_total'] ?? 0,
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    public function deleteAllItem($trspm_hd_id): void
    {
        $result = DB::delete(
            "DELETE FROM trspm_dt WHERE trspm_hd_id = :trspm_hd_id",
            [
                'trspm_hd_id' => $trspm_hd_id
            ]
        );
    }
}
