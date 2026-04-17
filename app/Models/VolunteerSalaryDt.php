<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class VolunteerSalaryDt extends BaseModel
{
    use HasFactory;

    protected $table = 'msvolunteersalarydt';

    public $timestamps = false;

    public function getAllData($hdCode)
    {
        return DB::select(
            "SELECT
                volunteer_salary_hd_code,
                volunteer_salary_dt_user_code,
                volunteer_salary_dt_user_name,
                isnull(volunteer_salary_dt_divisi, '') as volunteer_salary_dt_divisi,
                isnull(volunteer_salary_dt_work_day, 0) as volunteer_salary_dt_work_day,
                isnull(volunteer_salary_dt_price, 0) as volunteer_salary_dt_price,
                isnull(volunteer_salary_dt_bonuses, 0) as volunteer_salary_dt_bonuses,
                isnull(volunteer_salary_dt_total, 0) as volunteer_salary_dt_total
            from msvolunteersalarydt
            where volunteer_salary_hd_code = :hd_code
            order by volunteer_salary_dt_user_code",
            [
                'hd_code' => $hdCode,
            ]
        );
    }

    public function insertData($params)
    {
        return DB::insert(
            "INSERT INTO msvolunteersalarydt
            (volunteer_salary_hd_code, volunteer_salary_dt_user_code, volunteer_salary_dt_user_name, volunteer_salary_dt_divisi,
            volunteer_salary_dt_work_day, volunteer_salary_dt_price, volunteer_salary_dt_bonuses, volunteer_salary_dt_total)
            VALUES
            (:volunteer_salary_hd_code, :volunteer_salary_dt_user_code, :volunteer_salary_dt_user_name, :volunteer_salary_dt_divisi,
            :volunteer_salary_dt_work_day, :volunteer_salary_dt_price, :volunteer_salary_dt_bonuses, :volunteer_salary_dt_total)",
            [
                'volunteer_salary_hd_code' => $params['volunteer_salary_hd_code'],
                'volunteer_salary_dt_user_code' => $params['volunteer_salary_dt_user_code'],
                'volunteer_salary_dt_user_name' => $params['volunteer_salary_dt_user_name'],
                'volunteer_salary_dt_divisi' => $params['volunteer_salary_dt_divisi'],
                'volunteer_salary_dt_work_day' => $params['volunteer_salary_dt_work_day'],
                'volunteer_salary_dt_price' => $params['volunteer_salary_dt_price'],
                'volunteer_salary_dt_bonuses' => $params['volunteer_salary_dt_bonuses'],
                'volunteer_salary_dt_total' => $params['volunteer_salary_dt_total'],
            ]
        );
    }

    public function deleteByHdCode($hdCode)
    {
        return DB::delete(
            "DELETE FROM msvolunteersalarydt WHERE volunteer_salary_hd_code = :hd_code",
            [
                'hd_code' => $hdCode,
            ]
        );
    }
}
