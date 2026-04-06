<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VolunteerSalary extends BaseModel
{
    use HasFactory;

    protected $table = 'msvolunteersalary';

    public $timestamps = false;

    public function getAllData($params)
    {
        $result = DB::select(
            "SELECT
                volunteer_salary_code,
                volunteer_salary_instansi,
                volunteer_salary_date,
                volunteer_salary_date_from,
                volunteer_salary_date_to,
                volunteer_salary_name,
                isnull(volunteer_salary_position, '') as volunteer_salary_position,
                isnull(volunteer_salary_price, 0) as volunteer_salary_price,
                isnull(volunteer_salary_qty, 0) as volunteer_salary_qty,
                isnull(volunteer_salary_overtime, 0) as volunteer_salary_overtime,
                isnull(volunteer_salary_total, 0) as volunteer_salary_total,
                upddate,
                upduser
            from msvolunteersalary
                        where convert(varchar(8), volunteer_salary_date, 112) between :dari and :sampai
                            and (
                                        volunteer_salary_code like :search_keyword_code
                                 or volunteer_salary_name like :search_keyword_name
                                 or volunteer_salary_instansi like :search_keyword_instansi
                            )
            order by volunteer_salary_date desc, volunteer_salary_code desc",
            [
                                'dari' => $params['dari'],
                                'sampai' => $params['sampai'],
                                'search_keyword_code' => '%' . $params['search_keyword'] . '%',
                                'search_keyword_name' => '%' . $params['search_keyword'] . '%',
                                'search_keyword_instansi' => '%' . $params['search_keyword'] . '%'
            ]
        );

        return $result;
    }

    public function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT
                volunteer_salary_code,
                volunteer_salary_instansi,
                volunteer_salary_date,
                volunteer_salary_date_from,
                volunteer_salary_date_to,
                volunteer_salary_name,
                isnull(volunteer_salary_position, '') as volunteer_salary_position,
                isnull(volunteer_salary_price, 0) as volunteer_salary_price,
                isnull(volunteer_salary_qty, 0) as volunteer_salary_qty,
                isnull(volunteer_salary_overtime, 0) as volunteer_salary_overtime,
                isnull(volunteer_salary_total, 0) as volunteer_salary_total,
                upddate,
                upduser
            from msvolunteersalary
            where volunteer_salary_code = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT volunteer_salary_code from msvolunteersalary WHERE volunteer_salary_code = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO msvolunteersalary
            (volunteer_salary_code, volunteer_salary_instansi, volunteer_salary_date, volunteer_salary_date_from, volunteer_salary_date_to,
            volunteer_salary_name, volunteer_salary_position, volunteer_salary_price, volunteer_salary_qty, volunteer_salary_overtime,
            volunteer_salary_total, upddate, upduser)
            VALUES
            (:volunteer_salary_code, :volunteer_salary_instansi, :volunteer_salary_date, :volunteer_salary_date_from, :volunteer_salary_date_to,
            :volunteer_salary_name, :volunteer_salary_position, :volunteer_salary_price, :volunteer_salary_qty, :volunteer_salary_overtime,
            :volunteer_salary_total, getdate(), :upduser)",
            [
                'volunteer_salary_code' => $params['volunteer_salary_code'],
                'volunteer_salary_instansi' => $params['volunteer_salary_instansi'],
                'volunteer_salary_date' => $params['volunteer_salary_date'],
                'volunteer_salary_date_from' => $params['volunteer_salary_date_from'],
                'volunteer_salary_date_to' => $params['volunteer_salary_date_to'],
                'volunteer_salary_name' => $params['volunteer_salary_name'],
                'volunteer_salary_position' => $params['volunteer_salary_position'],
                'volunteer_salary_price' => $params['volunteer_salary_price'],
                'volunteer_salary_qty' => $params['volunteer_salary_qty'],
                'volunteer_salary_overtime' => $params['volunteer_salary_overtime'],
                'volunteer_salary_total' => $params['volunteer_salary_total'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    public function updateData($params)
    {
        $result = DB::update(
            "UPDATE msvolunteersalary SET
                volunteer_salary_instansi = :volunteer_salary_instansi,
                volunteer_salary_date = :volunteer_salary_date,
                volunteer_salary_date_from = :volunteer_salary_date_from,
                volunteer_salary_date_to = :volunteer_salary_date_to,
                volunteer_salary_name = :volunteer_salary_name,
                volunteer_salary_position = :volunteer_salary_position,
                volunteer_salary_price = :volunteer_salary_price,
                volunteer_salary_qty = :volunteer_salary_qty,
                volunteer_salary_overtime = :volunteer_salary_overtime,
                volunteer_salary_total = :volunteer_salary_total,
                upddate = getdate(),
                upduser = :upduser
            WHERE volunteer_salary_code = :volunteer_salary_code",
            [
                'volunteer_salary_code' => $params['volunteer_salary_code'],
                'volunteer_salary_instansi' => $params['volunteer_salary_instansi'],
                'volunteer_salary_date' => $params['volunteer_salary_date'],
                'volunteer_salary_date_from' => $params['volunteer_salary_date_from'],
                'volunteer_salary_date_to' => $params['volunteer_salary_date_to'],
                'volunteer_salary_name' => $params['volunteer_salary_name'],
                'volunteer_salary_position' => $params['volunteer_salary_position'],
                'volunteer_salary_price' => $params['volunteer_salary_price'],
                'volunteer_salary_qty' => $params['volunteer_salary_qty'],
                'volunteer_salary_overtime' => $params['volunteer_salary_overtime'],
                'volunteer_salary_total' => $params['volunteer_salary_total'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    public function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM msvolunteersalary WHERE volunteer_salary_code = :id",
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
            "SELECT isnull(max(cast(left(volunteer_salary_code, 3) as int)), 0) as max_seq
            from msvolunteersalary
            where month(volunteer_salary_date) = :month
              and year(volunteer_salary_date) = :year",
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
