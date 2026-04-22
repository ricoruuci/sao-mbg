<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Cabang;
use App\Models\BaseModel;

class TrAbsensiHd extends BaseModel
{
    use HasFactory;

    protected $table = 'trabsensihd';

    public $timestamps = false;

    protected $primaryKey = 'tr_absensi_header_code';

    public $incrementing = false;

    protected $keyType = 'string';

    public function details()
    {
        return $this->hasMany(TrAbsensiDt::class, 'tr_absensi_header_code', 'tr_absensi_header_code');
    }

    // Tambah method CRUD seperti getAllData, insertData, dll.
    function getAllData($params)
    {
        $result = DB::select(
            "SELECT tr_absensi_header_code, tr_absensi_header_date, tr_absensi_header_name, tr_absensi_header_company_id, tr_absensi_header_company_name,
            updated_at as upddate, isnull(upduser, '') as upduser
            FROM trabsensihd
            WHERE tr_absensi_header_name LIKE :search_keyword
            ORDER BY tr_absensi_header_date DESC",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%'
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT tr_absensi_header_code, tr_absensi_header_date, tr_absensi_header_name, tr_absensi_header_company_id, tr_absensi_header_company_name,
            updated_at as upddate, isnull(upduser, '') as upduser
            FROM trabsensihd
            WHERE tr_absensi_header_code = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function insertData($params)
    {
        // Ambil company_name dari mscabang
        $company_id = $params['tr_absensi_header_company_id'];
        $company_name = '';
        $cabang = (new Cabang())->cekData($company_id);
        if ($cabang && isset($cabang->company_name)) {
            $company_name = $cabang->company_name;
        }
        $result = DB::insert(
            "INSERT INTO trabsensihd (tr_absensi_header_code, tr_absensi_header_date, tr_absensi_header_name, tr_absensi_header_company_id, tr_absensi_header_company_name, upduser, created_at, updated_at)
            VALUES (:code, :date, :name, :company_id, :company_name, :upduser, getdate(), getdate())",
            [
                'code' => $params['tr_absensi_header_code'],
                'date' => $params['tr_absensi_header_date'],
                'name' => $params['tr_absensi_header_name'],
                'company_id' => $company_id,
                'company_name' => $company_name,
                'upduser' => $params['upduser']
            ]
        );
        return $result;
    }

    function updateData($params)
    {
        // Ambil company_name dari mscabang
        $company_id = $params['tr_absensi_header_company_id'];
        $company_name = '';
        $cabang = (new Cabang())->cekData($company_id);
        if ($cabang && isset($cabang->company_name)) {
            $company_name = $cabang->company_name;
        }
        $result = DB::update(
            "UPDATE trabsensihd SET
                tr_absensi_header_date = :date,
                tr_absensi_header_name = :name,
                tr_absensi_header_company_id = :company_id,
                tr_absensi_header_company_name = :company_name,
                upduser = :upduser,
                updated_at = getdate()
            WHERE tr_absensi_header_code = :code",
            [
                'code' => $params['tr_absensi_header_code'],
                'date' => $params['tr_absensi_header_date'],
                'name' => $params['tr_absensi_header_name'],
                'company_id' => $company_id,
                'company_name' => $company_name,
                'upduser' => $params['upduser']
            ]
        );
        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM trabsensihd WHERE tr_absensi_header_code = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($prefix)
    {
        $autoNumber = $this->autoNumber($this->table, 'tr_absensi_header_code', $prefix, '0000');

        return $autoNumber;
    }
}
