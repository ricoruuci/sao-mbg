<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

class TrAbsensiDt extends BaseModel
{
    use HasFactory;

    protected $table = 'trabsensidt';

    public $timestamps = false;

    protected $primaryKey = 'tr_absensi_dt_row_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public function header()
    {
        return $this->belongsTo(TrAbsensiHd::class, 'tr_absensi_header_code', 'tr_absensi_header_code');
    }

    // CRUD methods
    function getAllData($params)
    {
        $result = DB::select(
            "SELECT a.tr_absensi_dt_id, a.tr_absensi_header_code, a.tr_absensi_dt_name, a.tr_absensi_dt_date,
            a.tr_absensi_dt_clock_in, a.tr_absensi_dt_clock_out, a.tr_absensi_dt_nominal, a.updated_at as upddate, isnull(a.upduser, '') as upduser
            FROM trabsensidt a
            INNER JOIN trabsensihd b ON a.tr_absensi_header_code = b.tr_absensi_header_code
            WHERE b.tr_absensi_header_code = :header_code
            ORDER BY a.tr_absensi_dt_date",
            [
                'header_code' => $params['header_code']
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT tr_absensi_dt_id, tr_absensi_header_code, tr_absensi_dt_name, tr_absensi_dt_date,
            tr_absensi_dt_clock_in, tr_absensi_dt_clock_out, tr_absensi_dt_nominal, updated_at as upddate, isnull(upduser, '') as upduser
            FROM trabsensidt
            WHERE tr_absensi_dt_id = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO trabsensidt (tr_absensi_dt_id, tr_absensi_header_code, tr_absensi_dt_name, tr_absensi_dt_date, tr_absensi_dt_clock_in, tr_absensi_dt_clock_out, tr_absensi_dt_nominal, upduser, created_at, updated_at)
            VALUES (:id, :header_code, :name, :date, :clock_in, :clock_out, :nominal, :upduser, getdate(), getdate())",
            [
                'id' => $params['tr_absensi_dt_id'],
                'header_code' => $params['tr_absensi_header_code'],
                'name' => $params['tr_absensi_dt_name'],
                'date' => $params['tr_absensi_dt_date'],
                'clock_in' => $params['tr_absensi_dt_clock_in'],
                'clock_out' => $params['tr_absensi_dt_clock_out'],
                'nominal' => $params['tr_absensi_dt_nominal'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function upsertData($params)
    {
        $updated = DB::update(
            "UPDATE trabsensidt SET
                tr_absensi_dt_name = :name,
                tr_absensi_dt_clock_in = :clock_in,
                tr_absensi_dt_clock_out = :clock_out,
                tr_absensi_dt_nominal = :nominal,
                tr_absensi_dt_date = :date,
                upduser = :upduser,
                updated_at = getdate()
            WHERE tr_absensi_header_code = :header_code
              AND tr_absensi_dt_id = :id
                            AND ((tr_absensi_dt_date = :date_match_eq) OR (tr_absensi_dt_date IS NULL AND :date_match_null IS NULL))",
            [
                'id' => $params['tr_absensi_dt_id'],
                'header_code' => $params['tr_absensi_header_code'],
                'name' => $params['tr_absensi_dt_name'],
                'date' => $params['tr_absensi_dt_date'],
                                'date_match_eq' => $params['tr_absensi_dt_date'],
                                'date_match_null' => $params['tr_absensi_dt_date'],
                'clock_in' => $params['tr_absensi_dt_clock_in'],
                'clock_out' => $params['tr_absensi_dt_clock_out'],
                'nominal' => $params['tr_absensi_dt_nominal'],
                'upduser' => $params['upduser']
            ]
        );

        if ($updated > 0) {
            return true;
        }

        return $this->insertData($params);
    }

    function updateData($params)
    {
        $result = DB::update(
            "UPDATE trabsensidt SET
                tr_absensi_dt_name = :name,
                tr_absensi_dt_date = :date,
                tr_absensi_dt_clock_in = :clock_in,
                tr_absensi_dt_clock_out = :clock_out,
                tr_absensi_dt_nominal = :nominal,
                upduser = :upduser,
                updated_at = getdate()
            WHERE tr_absensi_dt_id = :id",
            [
                'id' => $params['tr_absensi_dt_id'],
                'name' => $params['tr_absensi_dt_name'],
                'date' => $params['tr_absensi_dt_date'],
                'clock_in' => $params['tr_absensi_dt_clock_in'],
                'clock_out' => $params['tr_absensi_dt_clock_out'],
                'nominal' => $params['tr_absensi_dt_nominal'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM trabsensidt WHERE tr_absensi_dt_id = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }
}
