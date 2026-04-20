<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VolunteerSalaryHd extends BaseModel
{
    use HasFactory;

    protected $table = 'msvolunteersalaryhd';

    public $timestamps = false;

    public function getAllData($params)
    {
        $result = DB::select(
            "SELECT
                volunteer_salary_hd_code,
                volunteer_salary_hd_date,
                volunteer_salary_hd_date_from,
                volunteer_salary_hd_date_to,
                isnull(volunteer_salary_hd_adjust, 0) as volunteer_salary_hd_adjust,
                isnull(volunteer_salary_hd_subtotal, 0) as volunteer_salary_hd_subtotal,
                isnull(volunteer_salary_hd_subbonuses, 0) as volunteer_salary_hd_subbonuses,
                isnull(volunteer_salary_hd_subtotal + volunteer_salary_hd_subbonuses, 0) as volunteer_salary_hd_grandtotal,
                isnull(volunteer_salary_hd_note, '') as volunteer_salary_hd_note,
                upddate,
                upduser
            from msvolunteersalaryhd
                        where convert(varchar(8), volunteer_salary_hd_date, 112) between :dari and :sampai
                            and (volunteer_salary_hd_code like :search_keyword_code
                                 or volunteer_salary_hd_note like :search_keyword_note
                            )
            order by volunteer_salary_hd_date desc, volunteer_salary_hd_code desc",
            [
                                'dari' => $params['dari'],
                                'sampai' => $params['sampai'],
                                'search_keyword_code' => '%' . $params['search_keyword'] . '%',
                                'search_keyword_note' => '%' . $params['search_keyword'] . '%'
            ]
        );

        return $result;
    }

    public function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT
                volunteer_salary_hd_code,
                volunteer_salary_hd_date,
                volunteer_salary_hd_date_from,
                volunteer_salary_hd_date_to,
                isnull(volunteer_salary_hd_adjust, 0) as volunteer_salary_hd_adjust,
                isnull(volunteer_salary_hd_subtotal, 0) as volunteer_salary_hd_subtotal,
                isnull(volunteer_salary_hd_subbonuses, 0) as volunteer_salary_hd_subbonuses,
                isnull(volunteer_salary_hd_subtotal + volunteer_salary_hd_subbonuses, 0) as volunteer_salary_hd_grandtotal,
                isnull((
                    SELECT TOP 1 b.tr_absensi_header_branch
                    FROM trabsensidt a
                    INNER JOIN trabsensihd b ON a.tr_absensi_header_code = b.tr_absensi_header_code
                    WHERE convert(varchar(8), a.tr_absensi_dt_date, 112)
                        BETWEEN convert(varchar(8), volunteer_salary_hd_date_from, 112)
                        AND convert(varchar(8), volunteer_salary_hd_date_to, 112)
                    ORDER BY a.tr_absensi_dt_date DESC, a.tr_absensi_header_code DESC
                ), '') as volunteer_salary_hd_branch,
                isnull(volunteer_salary_hd_note, '') as volunteer_salary_hd_note,
                upddate,
                upduser
            from msvolunteersalaryhd
            where volunteer_salary_hd_code = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT volunteer_salary_hd_code from msvolunteersalaryhd WHERE volunteer_salary_hd_code = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO msvolunteersalaryhd
            (volunteer_salary_hd_code, volunteer_salary_hd_date, volunteer_salary_hd_date_from, volunteer_salary_hd_date_to,
            volunteer_salary_hd_adjust, volunteer_salary_hd_subtotal, volunteer_salary_hd_subbonuses, volunteer_salary_hd_note, upddate, upduser)
            VALUES
            (:volunteer_salary_hd_code, :volunteer_salary_hd_date, :volunteer_salary_hd_date_from, :volunteer_salary_hd_date_to,
            :volunteer_salary_hd_adjust, :volunteer_salary_hd_subtotal, :volunteer_salary_hd_subbonuses, :volunteer_salary_hd_note, getdate(), :upduser)",
            [
                'volunteer_salary_hd_code' => $params['volunteer_salary_hd_code'],
                'volunteer_salary_hd_date' => $params['volunteer_salary_hd_date'],
                'volunteer_salary_hd_date_from' => $params['volunteer_salary_hd_date_from'],
                'volunteer_salary_hd_date_to' => $params['volunteer_salary_hd_date_to'],
                'volunteer_salary_hd_adjust' => $params['volunteer_salary_hd_adjust'],
                'volunteer_salary_hd_subtotal' => $params['volunteer_salary_hd_subtotal'],
                'volunteer_salary_hd_subbonuses' => $params['volunteer_salary_hd_subbonuses'],
                'volunteer_salary_hd_note' => $params['volunteer_salary_hd_note'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    public function updateData($params)
    {
        $result = DB::update(
            "UPDATE msvolunteersalaryhd SET
                volunteer_salary_hd_date = :volunteer_salary_hd_date,
                volunteer_salary_hd_date_from = :volunteer_salary_hd_date_from,
                volunteer_salary_hd_date_to = :volunteer_salary_hd_date_to,
                volunteer_salary_hd_adjust = :volunteer_salary_hd_adjust,
                volunteer_salary_hd_subtotal = :volunteer_salary_hd_subtotal,
                volunteer_salary_hd_subbonuses = :volunteer_salary_hd_subbonuses,
                volunteer_salary_hd_note = :volunteer_salary_hd_note,
                upddate = getdate(),
                upduser = :upduser
            WHERE volunteer_salary_hd_code = :volunteer_salary_hd_code",
            [
                'volunteer_salary_hd_code' => $params['volunteer_salary_hd_code'],
                'volunteer_salary_hd_date' => $params['volunteer_salary_hd_date'],
                'volunteer_salary_hd_date_from' => $params['volunteer_salary_hd_date_from'],
                'volunteer_salary_hd_date_to' => $params['volunteer_salary_hd_date_to'],
                'volunteer_salary_hd_adjust' => $params['volunteer_salary_hd_adjust'],
                'volunteer_salary_hd_subtotal' => $params['volunteer_salary_hd_subtotal'],
                'volunteer_salary_hd_subbonuses' => $params['volunteer_salary_hd_subbonuses'],
                'volunteer_salary_hd_note' => $params['volunteer_salary_hd_note'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    public function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM msvolunteersalaryhd WHERE volunteer_salary_hd_code = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($volunteerName, $salaryDate)
    {
        $parsedDate = Carbon::parse($salaryDate);
        $month = $parsedDate->format('m');
        $year = $parsedDate->format('Y');

        $initials = $this->buildInitials($volunteerName);

        $seqData = DB::selectOne(
            "SELECT isnull(max(cast(left(volunteer_salary_hd_code, 3) as int)), 0) as max_seq
            from msvolunteersalaryhd
            where month(volunteer_salary_hd_date) = :month
              and year(volunteer_salary_hd_date) = :year",
            [
                'month' => (int) $month,
                'year' => (int) $year,
            ]
        );

        $nextSeq = str_pad(((int) $seqData->max_seq) + 1, 3, '0', STR_PAD_LEFT);

        return $nextSeq . '/' . $initials . '/' . $month . '/' . $year;
    }

    private function buildInitials($name)
    {
        $normalized = preg_replace('/\s+/', ' ', trim((string) $name));

        if ($normalized === '') {
            return 'NA';
        }

        $parts = explode(' ', $normalized);
        $initials = '';

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            $initials .= strtoupper(substr($part, 0, 1));
        }

        return $initials !== '' ? $initials : 'NA';
    }
}
