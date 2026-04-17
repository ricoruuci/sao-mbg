<?php

namespace App\Http\Controllers;

use App\Http\Requests\VolunteerSalary\DeleteRequest;
use App\Http\Requests\VolunteerSalary\GetRequest;
use App\Http\Requests\VolunteerSalary\GetRequestById;
use App\Http\Requests\VolunteerSalary\InsertRequest;
use App\Http\Requests\VolunteerSalary\UpdateRequest;
use App\Models\VolunteerSalaryHd;
use App\Models\VolunteerSalaryDt;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VolunteerSalaryController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getDataById(GetRequestById $request)
    {
        $modelHeader = new VolunteerSalaryHd();
        $modelDetail = new VolunteerSalaryDt();

        $id = $request->volunteer_salary_hd_code;

        $cek = $modelHeader->cekData($id);
        if ($cek == false) {
            return $this->responseError('Data volunteer salary tidak ditemukan', 404);
        }

        $header = $modelHeader->getDataById($id);
        $detail = $modelDetail->getAllData($id);

        return $this->responseData([
            'header' => $header,
            'detail' => $detail,
        ]);
    }

    public function insertData(InsertRequest $request)
    {
        $modelHeader = new VolunteerSalaryHd();
        $modelDetail = new VolunteerSalaryDt();

        DB::beginTransaction();

        try {
            $autonumber = $modelHeader->beforeAutoNumber(
                $request->volunteer_salary_hd_note ?? 'Unknown',
                $request->volunteer_salary_hd_date
            );

            $params = [
                'volunteer_salary_hd_code' => $autonumber,
                'volunteer_salary_hd_date' => $request->volunteer_salary_hd_date,
                'volunteer_salary_hd_date_from' => $request->volunteer_salary_hd_date_from,
                'volunteer_salary_hd_date_to' => $request->volunteer_salary_hd_date_to,
                'volunteer_salary_hd_adjust' => $request->volunteer_salary_hd_adjust,
                'volunteer_salary_hd_subtotal' => $request->volunteer_salary_hd_subtotal,
                'volunteer_salary_hd_subbonuses' => $request->volunteer_salary_hd_subbonuses,
                'volunteer_salary_hd_note' => $request->volunteer_salary_hd_note,
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
            ];

            $insertResult = $modelHeader->insertData($params);

            if ($insertResult == false) {
                DB::rollBack();
                return $this->responseError('Gagal menyimpan data volunteer salary', 500);
            }

            $arrDetail = $request->input('detail');
            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();

                return $this->responseError('Detail volunteer salary tidak boleh kosong', 400);
            }

            foreach ($arrDetail as $item) {
                $workDay = (float) ($item['volunteer_salary_dt_work_day'] ?? 0);
                $price = (float) ($item['volunteer_salary_dt_price'] ?? 0);
                $bonuses = (float) ($item['volunteer_salary_dt_bonuses'] ?? 0);
                $total = $item['volunteer_salary_dt_total'] ?? (($workDay * $price) + $bonuses);

                $insertDetail = $modelDetail->insertData([
                    'volunteer_salary_hd_code' => $autonumber,
                    'volunteer_salary_dt_user_code' => $item['volunteer_salary_dt_user_code'],
                    'volunteer_salary_dt_user_name' => $item['volunteer_salary_dt_user_name'],
                    'volunteer_salary_dt_divisi' => $item['volunteer_salary_dt_divisi'] ?? '',
                    'volunteer_salary_dt_work_day' => $workDay,
                    'volunteer_salary_dt_price' => $price,
                    'volunteer_salary_dt_bonuses' => $bonuses,
                    'volunteer_salary_dt_total' => (float) $total,
                ]);

                if ($insertDetail == false) {
                    DB::rollBack();

                    return $this->responseError('Gagal menyimpan detail volunteer salary', 500);
                }
            }

            DB::commit();

            return $this->responseSuccess('Data volunteer salary berhasil disimpan', 200, [
                'volunteer_salary_hd_code' => $autonumber,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $modelHeader = new VolunteerSalaryHd();
        $modelDetail = new VolunteerSalaryDt();

        $cek = $modelHeader->cekData($request->volunteer_salary_hd_code);
        if ($cek == false) {
            return $this->responseError('Data volunteer salary tidak ditemukan', 404);
        }

        $params = [
            'volunteer_salary_hd_code' => $request->volunteer_salary_hd_code,
            'volunteer_salary_hd_date' => $request->volunteer_salary_hd_date,
            'volunteer_salary_hd_date_from' => $request->volunteer_salary_hd_date_from,
            'volunteer_salary_hd_date_to' => $request->volunteer_salary_hd_date_to,
            'volunteer_salary_hd_adjust' => $request->volunteer_salary_hd_adjust,
            'volunteer_salary_hd_subtotal' => $request->volunteer_salary_hd_subtotal,
            'volunteer_salary_hd_subbonuses' => $request->volunteer_salary_hd_subbonuses,
            'volunteer_salary_hd_note' => $request->volunteer_salary_hd_note,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        DB::beginTransaction();

        try {
            $updateResult = $modelHeader->updateData($params);

            if ($updateResult == false) {
                DB::rollBack();
                return $this->responseError('Gagal memperbarui data volunteer salary', 500);
            }

            $arrDetail = $request->input('detail');
            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();

                return $this->responseError('Detail volunteer salary tidak boleh kosong', 400);
            }

            $modelDetail->deleteByHdCode($request->volunteer_salary_hd_code);

            foreach ($arrDetail as $item) {
                $workDay = (float) ($item['volunteer_salary_dt_work_day'] ?? 0);
                $price = (float) ($item['volunteer_salary_dt_price'] ?? 0);
                $bonuses = (float) ($item['volunteer_salary_dt_bonuses'] ?? 0);
                $total = $item['volunteer_salary_dt_total'] ?? (($workDay * $price) + $bonuses);

                $insertDetail = $modelDetail->insertData([
                    'volunteer_salary_hd_code' => $request->volunteer_salary_hd_code,
                    'volunteer_salary_dt_user_code' => $item['volunteer_salary_dt_user_code'],
                    'volunteer_salary_dt_user_name' => $item['volunteer_salary_dt_user_name'],
                    'volunteer_salary_dt_divisi' => $item['volunteer_salary_dt_divisi'] ?? '',
                    'volunteer_salary_dt_work_day' => $workDay,
                    'volunteer_salary_dt_price' => $price,
                    'volunteer_salary_dt_bonuses' => $bonuses,
                    'volunteer_salary_dt_total' => (float) $total,
                ]);

                if ($insertDetail == false) {
                    DB::rollBack();

                    return $this->responseError('Gagal memperbarui detail volunteer salary', 500);
                }
            }

            DB::commit();

            return $this->responseSuccess('Data volunteer salary berhasil diperbarui', 200, [
                'volunteer_salary_hd_code' => $request->volunteer_salary_hd_code,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function deleteData(DeleteRequest $request)
    {
        $modelHeader = new VolunteerSalaryHd();
        $modelDetail = new VolunteerSalaryDt();

        $id = $request->volunteer_salary_hd_code;

        $cek = $modelHeader->cekData($id);
        if ($cek == false) {
            return $this->responseError('Data volunteer salary tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $modelDetail->deleteByHdCode($id);
            $deleteResult = $modelHeader->deleteData($id);

            if ($deleteResult == false) {
                DB::rollBack();

                return $this->responseError('Gagal menghapus data volunteer salary', 500);
            }

            DB::commit();

            return $this->responseSuccess('Data volunteer salary berhasil dihapus', 200, [
                'volunteer_salary_hd_code' => $id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function getListData(GetRequest $request)
    {
        $model = new VolunteerSalaryHd();

        $params = [
            'dari' => $request->dari,
            'sampai' => $request->sampai,
            'search_keyword' => $request->search_keyword ?? '',
        ];

        $result = $model->getAllData($params);
        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function getDataAbsensi(GetRequest $request)
    {
        $result = DB::select(
            "WITH absensi AS (
                SELECT
                    tr_absensi_dt_id AS employee_code,
                    COUNT(1) AS total_day
                FROM trabsensidt
                WHERE CONVERT(varchar(8), tr_absensi_dt_date, 112) BETWEEN :dari AND :sampai
                GROUP BY tr_absensi_dt_id
            )
            SELECT
                COALESCE(k.employee_code, a.employee_code) AS code,
                ISNULL(k.employee_name, '') AS name,
                ISNULL(k.employee_divisi, '') AS divisi,
                ISNULL(a.total_day, 0) AS day,
                ISNULL(k.salary_amount, 0) AS salary,
                CASE
                    WHEN k.employee_code IS NULL THEN 'Tidak ada di mskaryawan'
                    WHEN a.employee_code IS NULL THEN 'Tidak ada di trabsensidt'
                    ELSE ''
                END AS info
            FROM mskaryawan k
            FULL OUTER JOIN absensi a ON k.employee_code = a.employee_code
            WHERE k.employee_code IS NOT NULL OR a.employee_code IS NOT NULL
            ORDER BY COALESCE(k.employee_code, a.employee_code)",
            [
                'dari' => $request->dari,
                'sampai' => $request->sampai,
            ]
        );

        return $this->responseData($result);
    }
}
