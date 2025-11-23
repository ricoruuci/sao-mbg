<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class PayrollDt extends BaseModel
{
    use HasFactory;

    protected $table = 'payrolldt';

    public $timestamps = false;

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO payrolldt
            (id, employee_id, meal_amount, salary_amount, other_amount, present_days, total_amount, upddate, upduser)
            VALUES
            (:id, :employee_id, :meal_amount, :salary_amount, :other_amount, :present_days, :total_amount, getdate(), :upduser)",
            [
                'id' => $params['id'],
                'employee_id' => $params['employee_id'],
                'meal_amount' => $params['meal_amount'],
                'salary_amount' => $params['salary_amount'],
                'other_amount' => $params['other_amount'],
                'present_days' => $params['present_days'],
                'upduser' => $params['upduser'],
                'total_amount' => $params['total_amount']
            ]
        );


        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM payrolldt where id = :id ",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::select(
            "SELECT
            a.id,
            a.detail_id,
            a.employee_id,
            b.employee_code,
            b.employee_name,
            a.meal_amount,
            a.salary_amount,
            a.other_amount,
            a.present_days,
            a.total_amount,
            a.upddate,
            a.upduser
            from payrolldt a
            left join mskaryawan b on a.employee_id=b.id
            where a.id = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function deleteAllItem($id) : void
    {
        $result = DB::delete(
            "DELETE FROM AllItem where VoucherNo= :id",
            [
                'id' => $id
            ]
        );

    }

}

?>
