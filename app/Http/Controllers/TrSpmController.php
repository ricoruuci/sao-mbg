<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\TrSpmHd;
use App\Models\TrSpmDt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TrSpm\GetRequest;
use App\Http\Requests\TrSpm\InsertRequest;
use App\Http\Requests\TrSpm\UpdateRequest;
use App\Http\Requests\TrSpm\DeleteRequest;
use App\Http\Requests\TrSpm\GetRequestById;
use App\Http\Requests\TrSpm\AbsensiRequest;

class TrSpmController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(InsertRequest $request)
    {
        $model_header = new TrSpmHd();
        $model_detail = new TrSpmDt();

        // Lookup company_name dari mscabang
        $company_name = $model_header->getCompanyName($request->trspm_hd_company_id);

        $params = [
            'trspm_hd_date' => $request->trspm_hd_date,
            'trspm_hd_date_from' => $request->trspm_hd_date_from,
            'trspm_hd_date_to' => $request->trspm_hd_date_to,
            'trspm_hd_company_id' => $request->trspm_hd_company_id,
            'trspm_hd_company_name' => $company_name,
            'trspm_hd_work_days' => $request->trspm_hd_work_days ?? 0,
            'trspm_hd_overtime_adjustment' => $request->trspm_hd_overtime_adjustment ?? 0,
            'trspm_hd_subtotal' => $request->trspm_hd_subtotal ?? 0,
            'trspm_hd_subbonuses' => $request->trspm_hd_subbonuses ?? 0,
            'trspm_hd_note' => $request->trspm_hd_note ?? '',
            'upduser' => Auth::user()->currentAccessToken()['namauser'] ?? 'system',
        ];

        DB::beginTransaction();

        try {
            $hasilspmcode = $model_header->beforeAutoNumber($request->trspm_hd_company_id, $request->trspm_hd_date);

            $params['trspm_hd_code'] = $hasilspmcode;

            $insertheader = $model_header->insertData($params);

            if ($insertheader == false) {
                DB::rollBack();

                return $this->responseError('insert header gagal', 400);
            }

            $cekId = $model_header->cekCode($hasilspmcode);

            $arrDetail = $request->input('detail');

            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();

                return $this->responseError('detail tidak boleh kosong', 400);
            }

            for ($i = 0; $i < count($arrDetail); $i++) {

                $insertdetail = $model_detail->insertData([
                    'trspm_hd_id' => $cekId->id,
                    'trspm_dt_user_code' => $arrDetail[$i]['trspm_dt_user_code'],
                    'trspm_dt_user_name' => $arrDetail[$i]['trspm_dt_user_name'],
                    'trspm_dt_divisi' => $arrDetail[$i]['trspm_dt_divisi'] ?? '',
                    'trspm_dt_work_day' => $arrDetail[$i]['trspm_dt_work_day'] ?? 0,
                    'trspm_dt_price' => $arrDetail[$i]['trspm_dt_price'] ?? 0,
                    'trspm_dt_bonuses' => $arrDetail[$i]['trspm_dt_bonuses'] ?? 0,
                    'trspm_dt_overtime' => $arrDetail[$i]['trspm_dt_overtime'] ?? 0,
                    'trspm_dt_total' => $arrDetail[$i]['trspm_dt_total'] ?? 0,
                    'upduser' => Auth::user()->currentAccessToken()['namauser'] ?? 'system'
                ]);

                if ($insertdetail == false) {
                    DB::rollBack();

                    return $this->responseError('insert detail gagal', 400);
                }
            }

            $hitung = $model_header->hitungTotal($cekId->id);

            $model_header->updateTotal([
                'total' => (float) $hitung->total,
                'id' => $cekId->id,
            ]);

            DB::commit();

            return $this->responseSuccess('insert berhasil', 200, [
                'id' => $cekId->id,
                'trspm_hd_code' => $hasilspmcode
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $model_header = new TrSpmHd();
        $model_detail = new TrSpmDt();

        // cek data berdasarkan code
        $cok = $model_header->cekCode($request->trspm_hd_code ?? '');

        if ($cok == false) {
            return $this->responseError('nomor SPM tidak ada atau tidak ditemukan', 400);
        }

        // ambil nama company
        $company_name = $model_header->getCompanyName($request->trspm_hd_company_id);

        $params = [
            'trspm_hd_code' => $request->trspm_hd_code,
            'trspm_hd_date' => $request->trspm_hd_date,
            'trspm_hd_date_from' => $request->trspm_hd_date_from,
            'trspm_hd_date_to' => $request->trspm_hd_date_to,
            'trspm_hd_company_id' => $request->trspm_hd_company_id,
            'trspm_hd_company_name' => $company_name,
            'trspm_hd_work_days' => $request->trspm_hd_work_days ?? 0,
            'trspm_hd_overtime_adjustment' => $request->trspm_hd_overtime_adjustment ?? 0,
            'trspm_hd_subtotal' => $request->trspm_hd_subtotal ?? 0,
            'trspm_hd_subbonuses' => $request->trspm_hd_subbonuses ?? 0,
            'trspm_hd_note' => $request->trspm_hd_note ?? '',
            'upduser' => Auth::user()->currentAccessToken()['namauser'] ?? 'system',
        ];

        DB::beginTransaction();

        try {
            // update header
            $updateHeader = $model_header->updateData($params);

            if ($updateHeader == false) {
                DB::rollBack();
                return $this->responseError('update header gagal', 400);
            }

            $arrDetail = $request->input('detail');

            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();
                return $this->responseError('detail tidak boleh kosong', 400);
            }

            // hapus detail lama
            $model_detail->deleteData($cok->id);

            // insert ulang detail
            foreach ($arrDetail as $row) {

                $insertdetail = $model_detail->insertData([
                    'trspm_hd_id' => $cok->id,
                    'trspm_dt_user_code' => $row['trspm_dt_user_code'] ?? '',
                    'trspm_dt_user_name' => $row['trspm_dt_user_name'] ?? '',
                    'trspm_dt_divisi' => $row['trspm_dt_divisi'] ?? '',
                    'trspm_dt_work_day' => $row['trspm_dt_work_day'] ?? 0,
                    'trspm_dt_price' => $row['trspm_dt_price'] ?? 0,
                    'trspm_dt_bonuses' => $row['trspm_dt_bonuses'] ?? 0,
                    'trspm_dt_overtime' => $row['trspm_dt_overtime'] ?? 0,
                    'trspm_dt_total' => $row['trspm_dt_total'] ?? 0,
                    'upduser' => Auth::user()->currentAccessToken()['namauser'] ?? 'system'
                ]);

                if ($insertdetail == false) {
                    DB::rollBack();
                    return $this->responseError('update detail gagal', 400);
                }
            }

            // hitung total ulang
            $hitung = $model_header->hitungTotal($cok->id);

            $model_header->updateTotal([
                'total' => (float) ($hitung->total ?? 0),
                'id' => $cok->id,
            ]);

            DB::commit();

            return $this->responseSuccess('update berhasil', 200, [
                'trspm_hd_code' => $request->trspm_hd_code
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function getListData(GetRequest $request)
    {
        $model = new TrSpmHd();

        $params = [
            'dari' => $request->dari,
            'sampai' => $request->sampai,
            'trspm_hd_code_keyword' => $request->search_keyword ?? '',
        ];

        $result = $model->getAllData($params);
        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function getDataById(GetRequestById $request)
    {
        $model_header = new TrSpmHd();
        $model_detail = new TrSpmDt();

        $result = $model_header->getDataById($request->trspm_hd_code ?? '');

        if ($result) {
            $header = $result;
            $detail_result = $model_detail->getDataById($result->id ?? '');
            $detail = !empty($detail_result) ? $detail_result : [];
        } else {
            $header = [];
            $detail = [];
        }

        $response = [
            'header' => $header,
            'detail' => $detail,
        ];

        return $this->responseData($response);
    }

    public function deleteData(DeleteRequest $request)
    {
        $model = new TrSpmHd();

        $code = $request->trspm_hd_code;

        $cek = $model->cekCode($request->trspm_hd_code);
        if ($cek == false) {
            return $this->responseError('nomor SPM tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $deleteResult = $model->deleteDataByCode($code);

            DB::commit();
            return $this->responseSuccess('Data SPM berhasil dihapus', 200, [
                'id' => $cek->id,
                'trspm_hd_code' => $code
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function getDataAbsensi(AbsensiRequest $request)
    {
        try {
            $dari = $request->dari;
            $sampai = $request->sampai;
            $company_id = (string) $request->company_id;

            $sql = "WITH absensi AS (
                SELECT
                    d.tr_absensi_dt_id AS employee_code,
                    COUNT(DISTINCT CONVERT(VARCHAR(8), d.tr_absensi_dt_date, 112)) AS total_day
                FROM trabsensidt d
                INNER JOIN trabsensihd h ON d.tr_absensi_header_code = h.tr_absensi_header_code
                WHERE CONVERT(VARCHAR(8), d.tr_absensi_dt_date, 112) BETWEEN ? AND ?
                AND (d.tr_absensi_dt_clock_in IS NOT NULL OR d.tr_absensi_dt_clock_out IS NOT NULL)
                AND h.tr_absensi_header_company_id = ?
                GROUP BY d.tr_absensi_dt_id
            )
            SELECT
                k.employee_code AS code,
                ISNULL(k.employee_name, '') AS name,
                ISNULL(k.employee_divisi, '') AS divisi,
                ISNULL(a.total_day, 0) AS day,
                ISNULL(k.salary_amount, 0) AS salary
            FROM mskaryawan k
            LEFT JOIN absensi a ON k.employee_code = a.employee_code
            WHERE k.company_id = ?
            ORDER BY k.employee_code";
            
            $result = DB::select($sql, [$dari, $sampai, $company_id, $company_id]);
            
            return $this->responseData($result);
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }
}
