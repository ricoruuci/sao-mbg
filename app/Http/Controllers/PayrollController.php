<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\PayrollHd;
use App\Models\PayrollDt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Payroll\InsertRequest;
use App\Http\Requests\Payroll\UpdateRequest;
use App\Http\Requests\Payroll\DeleteRequest;
use App\Http\Requests\Payroll\GetRequestById;
use App\Http\Requests\Payroll\GetRequest;

class PayrollController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(InsertRequest $request)
    {
        $model_header = new PayrollHd();
        $model_detail = new PayrollDt();
        $model_employee = new Employee();

        $params = [
            'tx_date' => $request->tx_date,
            'tx_period' => $request->tx_period,
            'work_days' => $request->work_days ?? 0,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
            'company_id' => $request->company_id,
        ];

        DB::beginTransaction();

        try {
            $hasilpoid = $model_header->beforeAutoNumber($request->company_id,$request->tx_period);

            $params['tx_id'] = $hasilpoid;

            $insertheader = $model_header->insertData($params);

            if ($insertheader == false) {
                DB::rollBack();

                return $this->responseError('insert header gagal', 400);
            }

            $cekId = $model_header->cekId($hasilpoid);

            $arrDetail = $request->input('detail');

            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();

                return $this->responseError('detail tidak boleh kosong', 400);
            }

            for ($i = 0; $i < sizeof($arrDetail); $i++) {

                $cek = $model_employee->cekData($arrDetail[$i]['employee_id'] ?? '');

                if ($cek == false) {
                    DB::rollBack();

                    return $this->responseError('Karyawan tidak ada atau tidak ditemukan', 400);
                }

                $insertdetail = $model_detail->insertData([
                    'id' => $cekId->id,
                    'employee_id' => $arrDetail[$i]['employee_id'],
                    'meal_amount' => $arrDetail[$i]['meal_amount'],
                    'salary_amount' => $arrDetail[$i]['salary_amount'],
                    'other_amount' => $arrDetail[$i]['other_amount'],
                    'present_days' => $arrDetail[$i]['present_days'],
                    'total_amount' => (($arrDetail[$i]['meal_amount'] ?? 0)*($arrDetail[$i]['present_days'] ?? 0)) + ($arrDetail[$i]['salary_amount'] ?? 0) + ($arrDetail[$i]['other_amount'] ?? 0),
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
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
                'tx_id' => $hasilpoid
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }

    }

    public function UpdateData(UpdateRequest $request)
    {
        $model_header = new PayrollHd();
        $model_detail = new PayrollDt();
        $model_employee = new Employee();

        $cok = $model_header->cekData($request->id ?? '');

        if ($cok == false) {

            return $this->responseError('nomor payroll tidak ada atau tidak ditemukan', 400);
        }

        $params = [
            'id' => $request->id,
            'tx_date' => $request->tx_date,
            'tx_period' => $request->tx_period,
            'work_days' => $request->work_days ?? 0,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
            'company_id' => $request->company_id,
        ];

        DB::beginTransaction();

        try {

            $insertheader = $model_header->updateData($params);

            if ($insertheader == false) {
                DB::rollBack();

                return $this->responseError('update header gagal', 400);
            }

            $arrDetail = $request->input('detail');

            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();

                return $this->responseError('detail tidak boleh kosong', 400);
            }

            $model_detail->deleteData($request->id);

            for ($i = 0; $i < sizeof($arrDetail); $i++) {

                $cek = $model_employee->cekData($arrDetail[$i]['employee_id'] ?? '');

                if ($cek == false) {
                    DB::rollBack();

                    return $this->responseError('karyawan tidak ada atau tidak ditemukan', 400);
                }

                $insertdetail = $model_detail->insertData([
                    'id' => $request->id,
                    'employee_id' => $arrDetail[$i]['employee_id'],
                    'meal_amount' => $arrDetail[$i]['meal_amount'],
                    'salary_amount' => $arrDetail[$i]['salary_amount'],
                    'other_amount' => $arrDetail[$i]['other_amount'],
                    'present_days' => $arrDetail[$i]['present_days'],
                    'total_amount' => (($arrDetail[$i]['meal_amount'] ?? 0)*($arrDetail[$i]['present_days'] ?? 0)) + ($arrDetail[$i]['salary_amount'] ?? 0) + ($arrDetail[$i]['other_amount'] ?? 0),
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);

                if ($insertdetail == false) {
                    DB::rollBack();

                    return $this->responseError('update detail gagal', 400);
                }
            }

            $hitung = $model_header->hitungTotal($request->id);

            //dd(var_dump($hitung));
            $model_header->updateTotal([
                'total' => (float) $hitung->total,
                'id' => $request->id,
            ]);

            DB::commit();

            return $this->responseSuccess('update berhasil', 200, [
                'id' => $request->id,
                'tx_id' => $cok->tx_id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }

    }

    public function getListData(GetRequest $request)
    {
        $model = new PayrollHd();

        $result = $model->getAllData([
            'dari' => $request->dari,
            'sampai' => $request->sampai,
            'tx_id_keyword' => $request->tx_id_keyword,
            'tx_period_keyword' => $request->tx_period_keyword,
            'company_id' => $request->company_id,
        ]);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

    public function getDataById(GetRequestById $request)
    {
        $model_header = new PayrollHd();

        $model_detail = new PayrollDt();

        $result = $model_header->getDataById($request->id ?? '');

        if ($result) {
            $header = $result;

            $detail_result = $model_detail->getDataById($result->id ?? '');

            $detail = !empty($detail_result) ? $detail_result : [];

        }
        else {
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
        $model = new PayrollHd();

        $id = $request->id;

        $cek = $model->cekData($request->id);
        if ($cek == false) {
            return $this->responseError('nomor payroll tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {

            $deleteResult = $model->deleteData($id);

            DB::commit();
            return $this->responseSuccess('Data Nota Beli berhasil dihapus', 200, [
                'id' => $id,
                'tx_id' => $cek->tx_id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

}

?>
