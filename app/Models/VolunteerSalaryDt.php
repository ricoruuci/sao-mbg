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
            (trbreakdownmenudt_hd_code, trbreakdownmenudt_itemid, trbreakdownmenudt_qty, trbreakdownmenudt_uomid,
            trbreakdownmenudt_note)
            VALUES
            (:trbreakdownmenudt_hd_code, :trbreakdownmenudt_itemid, :trbreakdownmenudt_qty, :trbreakdownmenudt_uomid,
            :trbreakdownmenudt_note)",
            [
                'trbreakdownmenudt_hd_code' => $params['trbreakdownmenudt_hd_code'],
                'trbreakdownmenudt_itemid' => $params['trbreakdownmenudt_itemid'],
                'trbreakdownmenudt_qty' => $params['trbreakdownmenudt_qty'],
                'trbreakdownmenudt_uomid' => $params['trbreakdownmenudt_uomid'],
                'trbreakdownmenudt_note' => $params['trbreakdownmenudt_note'],
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
