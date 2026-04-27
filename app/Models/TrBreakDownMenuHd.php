<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Cabang;

class TrBreakDownMenuHd extends BaseModel
{
    use HasFactory;

    protected $table = 'trbreakdownmenuhd';

    public $timestamps = false;

    public function getAllData($params)
    {
        $result = DB::select(
            "SELECT
                trbreakdownhd_code,
                trbreakdownhd_date,
                isnull(trbreakdownhd_qty_beneficiaries, 0) as trbreakdownhd_qty_beneficiaries,
                isnull(trbreakdownhd_note, '') as trbreakdownhd_note,
                isnull(trbreakdownhd_company_id, '') as trbreakdownhd_company_id,
                isnull(trbreakdownhd_company_name, '') as trbreakdownhd_company_name,
                upddate,
                upduser
            from trbreakdownmenuhd
            where convert(varchar(8), trbreakdownhd_date, 112) between :dari and :sampai
                and (trbreakdownhd_code like :search_keyword_code
                     or trbreakdownhd_note like :search_keyword_note
                )
            order by trbreakdownhd_date desc, trbreakdownhd_code desc",
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
                trbreakdownhd_code,
                trbreakdownhd_date,
                isnull(trbreakdownhd_qty_beneficiaries, 0) as trbreakdownhd_qty_beneficiaries,
                isnull(trbreakdownhd_note, '') as trbreakdownhd_note,
                isnull(trbreakdownhd_company_id, '') as trbreakdownhd_company_id,
                isnull(trbreakdownhd_company_name, '') as trbreakdownhd_company_name,
                upddate,
                upduser
            from trbreakdownmenuhd
            where trbreakdownhd_code = :id",
            [
                'id' => $id
            ]
        );
        return $result;
    }


    public function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT trbreakdownhd_code from trbreakdownmenuhd WHERE trbreakdownhd_code = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function insertData($params)
    {
        // Ambil company_name dari mscabang
        $company_id = $params['trbreakdownhd_company_id'] ?? null;
        $company_name = '';
        if ($company_id) {
            $cabang = (new Cabang())->cekData($company_id);
            if ($cabang && isset($cabang->company_name)) {
                $company_name = $cabang->company_name;
            }
        }

        // Generate kode otomatis BD001, BD002, dst
        $last = DB::selectOne("SELECT MAX(CAST(SUBSTRING(trbreakdownhd_code, 3, 3) AS INT)) as max_num FROM trbreakdownmenuhd WHERE trbreakdownhd_code LIKE 'BD%'");
        $nextNumber = 1;
        if ($last && $last->max_num) {
            $nextNumber = $last->max_num + 1;
        }
        $kodeBaru = 'BD' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $result = DB::insert(
            "INSERT INTO trbreakdownmenuhd
            (trbreakdownhd_code, trbreakdownhd_date, trbreakdownhd_qty_beneficiaries,
            trbreakdownhd_company_id, trbreakdownhd_company_name,
            trbreakdownhd_note, upddate, upduser)
            VALUES
            (:trbreakdownhd_code, :trbreakdownhd_date, :trbreakdownhd_qty_beneficiaries,
            :trbreakdownhd_company_id, :trbreakdownhd_company_name, :trbreakdownhd_note, getdate(), :upduser)",
            [
                'trbreakdownhd_code' => $kodeBaru,
                'trbreakdownhd_date' => $params['trbreakdownhd_date'],
                'trbreakdownhd_qty_beneficiaries' => $params['trbreakdownhd_qty_beneficiaries'],
                'trbreakdownhd_company_id' => $company_id,
                'trbreakdownhd_company_name' => $company_name,
                'trbreakdownhd_note' => $params['trbreakdownhd_note'],
                'upduser' => $params['upduser']
            ]
        );
        // Return kode header yang baru dibuat agar bisa dipakai insert detail
        return $kodeBaru;
    }

    public function updateData($params)
    {
        // Ambil company_name dari mscabang
        $company_id = $params['trbreakdownhd_company_id'] ?? null;
        $company_name = '';
        if ($company_id) {
            $cabang = (new Cabang())->cekData($company_id);
            if ($cabang && isset($cabang->company_name)) {
                $company_name = $cabang->company_name;
            }
        }
        $result = DB::update(
            "UPDATE trbreakdownmenuhd SET
                trbreakdownhd_date = :trbreakdownhd_date,
                trbreakdownhd_qty_beneficiaries = :trbreakdownhd_qty_beneficiaries,
                trbreakdownhd_company_id = :trbreakdownhd_company_id,
                trbreakdownhd_company_name = :trbreakdownhd_company_name,
                trbreakdownhd_note = :trbreakdownhd_note,
                upddate = getdate(),
                upduser = :upduser
            WHERE trbreakdownhd_code = :trbreakdownhd_code",
            [
                'trbreakdownhd_code' => $params['trbreakdownhd_code'],
                'trbreakdownhd_date' => $params['trbreakdownhd_date'],
                'trbreakdownhd_qty_beneficiaries' => $params['trbreakdownhd_qty_beneficiaries'],
                'trbreakdownhd_company_id' => $company_id,
                'trbreakdownhd_company_name' => $company_name,
                'trbreakdownhd_note' => $params['trbreakdownhd_note'],
                'upduser' => $params['upduser']
            ]
        );
        return $result;
    }

    public function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM trbreakdownmenuhd WHERE trbreakdownhd_code = :id",
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
