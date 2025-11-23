<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class Employee extends BaseModel
{
    use HasFactory;

    protected $table = 'mskaryawan';

    public $timestamps = false;

    function getAllData($params)
    {
        $result = DB::select(
            "SELECT a.id, a.employee_code, a.employee_name, a.join_date, a.fg_active, a.meal_amount, a.salary_amount, a.other_amount, a.upddate, a.upduser,
            a.company_id,b.company_code,b.company_name,b.company_address
            from mskaryawan a
            left join mscabang b on a.company_id = b.company_id
            where ( a.employee_name like :search_keyword1 or a.employee_code like :search_keyword2 )
            and a.fg_active like :search_keyword3
            order by a.employee_name ",
            [
                'search_keyword1' => '%' . $params['search_keyword'] . '%',
                'search_keyword2' => '%' . $params['search_keyword'] . '%',
                'search_keyword3' => '%' . $params['fg_active'] . '%',
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT a.id, a.employee_code, a.employee_name, a.join_date, a.fg_active, a.meal_amount, a.salary_amount, a.other_amount, a.upddate, a.upduser,
            a.company_id,b.company_code,b.company_name,b.company_address
            from mskaryawan a
            left join mscabang b on a.company_id = b.company_id
            where a.id = :id ",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT * from mskaryawan WHERE id = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekId($code)
    {
        $result = DB::selectOne(
            'SELECT * from mskaryawan WHERE employee_code = :employee_code',
            [
                'employee_code' => $code
            ]
        );

        return $result;
    }

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO mskaryawan (employee_code, employee_name, join_date, fg_active, meal_amount, salary_amount, other_amount, upddate, upduser, company_id)
            VALUES (:employee_code, :employee_name, :join_date, :fg_active, :meal_amount, :salary_amount, :other_amount, getdate(), :upduser, :company_id)",
            [
                'employee_code' => $params['employee_code'],
                'employee_name' => $params['employee_name'],
                'join_date' => $params['join_date'],
                'fg_active' => $params['fg_active'],
                'meal_amount' => $params['meal_amount'],
                'salary_amount' => $params['salary_amount'],
                'other_amount' => $params['other_amount'],
                'upduser' => $params['upduser'],
                'company_id' => $params['company_id']
            ]
        );


        return $result;
    }

    function updateData($params)
    {
        $result = DB::update(
            "UPDATE mskaryawan
            SET
            -- employee_code = :employee_code,
            employee_name = :employee_name,
            join_date = :join_date,
            fg_active = :fg_active,
            meal_amount = :meal_amount,
            salary_amount = :salary_amount,
            other_amount = :other_amount,
            upddate = getdate(),
            upduser = :upduser,
            company_id = :company_id
            WHERE id = :id ",
            [
                'id' => $params['id'],
                // 'employee_code' => $params['employee_code'],
                'employee_name' => $params['employee_name'],
                'join_date' => $params['join_date'],
                'fg_active' => $params['fg_active'],
                'meal_amount' => $params['meal_amount'],
                'salary_amount' => $params['salary_amount'],
                'other_amount' => $params['other_amount'],
                'upduser' => $params['upduser'],
                'company_id' => $params['company_id']
            ]
        );

        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM mskaryawan WHERE id = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($yearmonth,$company_id)
    {
        $autoNumber = $this->autoNumber($this->table, 'employee_code', 'ID'.$yearmonth.$company_id, '0000');

        return $autoNumber;
    }

}

?>
