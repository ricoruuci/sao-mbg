<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Yayasan extends BaseModel
{
    use HasFactory;

    protected $table = 'msyayasan';

    public $timestamps = false;

    public function getAllData($params)
    {
        $result = DB::select(
            "SELECT
                yayasan_code,
                yayasan_name,
                isnull(yayasan_address, '') as yayasan_address,
                isnull(yayasan_note, '') as yayasan_note,
                isnull(yayasan_phone, '') as yayasan_phone,
                isnull(yayasan_email, '') as yayasan_email,
                upddate,
                upduser
            from msyayasan
            where yayasan_name like :search_keyword
            order by yayasan_name",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%'
            ]
        );

        return $result;
    }

    public function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT
                yayasan_code,
                yayasan_name,
                isnull(yayasan_address, '') as yayasan_address,
                isnull(yayasan_note, '') as yayasan_note,
                isnull(yayasan_phone, '') as yayasan_phone,
                isnull(yayasan_email, '') as yayasan_email,
                upddate,
                upduser
            from msyayasan
            where yayasan_code = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT * from msyayasan WHERE yayasan_code = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO msyayasan
            (yayasan_code, yayasan_name, yayasan_address, yayasan_note, yayasan_phone, yayasan_email, upddate, upduser)
            VALUES
            (:yayasan_code, :yayasan_name, :yayasan_address, :yayasan_note, :yayasan_phone, :yayasan_email, getdate(), :upduser)",
            [
                'yayasan_code' => $params['yayasan_code'],
                'yayasan_name' => $params['yayasan_name'],
                'yayasan_address' => $params['yayasan_address'],
                'yayasan_note' => $params['yayasan_note'],
                'yayasan_phone' => $params['yayasan_phone'],
                'yayasan_email' => $params['yayasan_email'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    public function updateData($params)
    {
        $result = DB::update(
            "UPDATE msyayasan SET
                yayasan_name = :yayasan_name,
                yayasan_address = :yayasan_address,
                yayasan_note = :yayasan_note,
                yayasan_phone = :yayasan_phone,
                yayasan_email = :yayasan_email,
                upddate = getdate(),
                upduser = :upduser
            WHERE yayasan_code = :yayasan_code",
            [
                'yayasan_code' => $params['yayasan_code'],
                'yayasan_name' => $params['yayasan_name'],
                'yayasan_address' => $params['yayasan_address'],
                'yayasan_note' => $params['yayasan_note'],
                'yayasan_phone' => $params['yayasan_phone'],
                'yayasan_email' => $params['yayasan_email'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    public function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM msyayasan WHERE yayasan_code = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber()
    {
        $autoNumber = $this->autoNumber($this->table, 'yayasan_code', 'YS-', '0000');

        return $autoNumber;
    }
}
