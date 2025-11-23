<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Http\Requests\Employee\InsertRequest;
use App\Http\Requests\Employee\UpdateRequest;
use App\Http\Requests\Employee\DeleteRequest;
use App\Http\Requests\Employee\GetRequest;
use App\Http\Requests\Employee\GetRequestById;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getDataById(GetRequestById $request)
    {
        $employee_model = new Employee();

        $id = $request->id;

        $cek = $employee_model->cekData($id);
        if ($cek == false) {
            return $this->responseError('Karyawan tidak ditemukan', 404);
        }

        $result = $employee_model->getDataById($id);

        return $this->responseData($result);
    }

    public function getListData(GetRequest $request)
    {
        $group_model = new Employee();

        $params = [
            'search_keyword' => $request->search_keyword ?? '',
            'fg_active' => $request->fg_active ?? '',
        ];

        $result = $group_model->getAllData($params);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

    public function insertData(InsertRequest $request)
    {
        $model = new Employee();

        DB::beginTransaction();

        try {
            $autonumber = $model->beforeAutoNumber(substr($request->join_date, 2, 4), $request->company_id);

            $params = [
                'employee_code' => $autonumber,
                'employee_name' => $request->employee_name,
                'join_date' => $request->join_date,
                'fg_active' => $request->fg_active ?? 'Y',
                'meal_amount' => $request->meal_amount ?? 0,
                'salary_amount' => $request->salary_amount ?? 0,
                'other_amount' => $request->other_amount ?? 0,
                'company_id' => $request->company_id,
                'upduser' => Auth::user()->currentAccessToken()['namauser'],

            ];

            $insertResult = $model->insertData($params);

            if ($insertResult == false) {
                return $this->responseError('Gagal menyimpan data karyawan', 500);
            }

            DB::commit();

            $result = $model->cekId($autonumber);

            return $this->responseSuccess('Data karyawan berhasil disimpan', 200, [
                'id' => $result->id,
                'employee_code' => $autonumber
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $model = new Employee();

        $params = [
            'id' => $request->id,
            // 'employee_code' => $request->employee_code,
            'employee_name' => $request->employee_name,
            'join_date' => $request->join_date,
            'fg_active' => $request->fg_active ?? 'Y',
            'meal_amount' => $request->meal_amount ?? 0,
            'salary_amount' => $request->salary_amount ?? 0,
            'other_amount' => $request->other_amount ?? 0,
            'company_id' => $request->company_id,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        $cek = $model->cekData($request->id);
        if ($cek == false) {
            return $this->responseError('Karyawan tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $updateResult = $model->updateData($params);

            if ($updateResult == false) {
                return $this->responseError('Gagal memperbarui data karyawan', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data karyawan berhasil diperbarui', 200, [
                'id' => $request->id,
                'employee_code' => $cek->employee_code
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
    public function deleteData(DeleteRequest $request)
    {
        $model = new Employee();

        $id = $request->id;

        $cek = $model->cekData($request->id);
        if ($cek == false) {
            return $this->responseError('Karyawan tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $deleteResult = $model->deleteData($id);

            if ($deleteResult == false) {
                return $this->responseError('Gagal menghapus data karyawan', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data karyawan berhasil dihapus', 200, ['id' => $id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

}

?>

