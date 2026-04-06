<?php

namespace App\Http\Controllers;

use App\Http\Requests\VolunteerSalary\DeleteRequest;
use App\Http\Requests\VolunteerSalary\GetRequest;
use App\Http\Requests\VolunteerSalary\GetRequestById;
use App\Http\Requests\VolunteerSalary\InsertRequest;
use App\Http\Requests\VolunteerSalary\UpdateRequest;
use App\Models\VolunteerSalary;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VolunteerSalaryController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getDataById(GetRequestById $request)
    {
        $model = new VolunteerSalary();

        $id = $request->volunteer_salary_code;

        $cek = $model->cekData($id);
        if ($cek == false) {
            return $this->responseError('Data volunteer salary tidak ditemukan', 404);
        }

        $result = $model->getDataById($id);

        return $this->responseData($result);
    }

    public function insertData(InsertRequest $request)
    {
        $model = new VolunteerSalary();

        DB::beginTransaction();

        try {
            $autonumber = $model->beforeAutoNumber(
                $request->volunteer_salary_name,
                $request->volunteer_salary_date
            );

            $price = (float) ($request->volunteer_salary_price ?? 0);
            $qty = (float) ($request->volunteer_salary_qty ?? 0);
            $overtime = (float) ($request->volunteer_salary_overtime ?? 0);
            $total = $request->volunteer_salary_total ?? (($price * $qty) + $overtime);

            $params = [
                'volunteer_salary_code' => $autonumber,
                'volunteer_salary_instansi' => $request->volunteer_salary_instansi,
                'volunteer_salary_date' => $request->volunteer_salary_date,
                'volunteer_salary_date_from' => $request->volunteer_salary_date_from,
                'volunteer_salary_date_to' => $request->volunteer_salary_date_to,
                'volunteer_salary_name' => $request->volunteer_salary_name,
                'volunteer_salary_position' => $request->volunteer_salary_position,
                'volunteer_salary_price' => $price,
                'volunteer_salary_qty' => $qty,
                'volunteer_salary_overtime' => $overtime,
                'volunteer_salary_total' => (float) $total,
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
            ];

            $insertResult = $model->insertData($params);

            if ($insertResult == false) {
                DB::rollBack();

                return $this->responseError('Gagal menyimpan data volunteer salary', 500);
            }

            DB::commit();

            return $this->responseSuccess('Data volunteer salary berhasil disimpan', 200, [
                'volunteer_salary_code' => $autonumber,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $model = new VolunteerSalary();

        $cek = $model->cekData($request->volunteer_salary_code);
        if ($cek == false) {
            return $this->responseError('Data volunteer salary tidak ditemukan', 404);
        }

        $price = (float) ($request->volunteer_salary_price ?? 0);
        $qty = (float) ($request->volunteer_salary_qty ?? 0);
        $overtime = (float) ($request->volunteer_salary_overtime ?? 0);
        $total = $request->volunteer_salary_total ?? (($price * $qty) + $overtime);

        $params = [
            'volunteer_salary_code' => $request->volunteer_salary_code,
            'volunteer_salary_instansi' => $request->volunteer_salary_instansi,
            'volunteer_salary_date' => $request->volunteer_salary_date,
            'volunteer_salary_date_from' => $request->volunteer_salary_date_from,
            'volunteer_salary_date_to' => $request->volunteer_salary_date_to,
            'volunteer_salary_name' => $request->volunteer_salary_name,
            'volunteer_salary_position' => $request->volunteer_salary_position,
            'volunteer_salary_price' => $price,
            'volunteer_salary_qty' => $qty,
            'volunteer_salary_overtime' => $overtime,
            'volunteer_salary_total' => (float) $total,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        DB::beginTransaction();

        try {
            $updateResult = $model->updateData($params);

            if ($updateResult == false) {
                DB::rollBack();

                return $this->responseError('Gagal memperbarui data volunteer salary', 500);
            }

            DB::commit();

            return $this->responseSuccess('Data volunteer salary berhasil diperbarui', 200, [
                'volunteer_salary_code' => $request->volunteer_salary_code,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function deleteData(DeleteRequest $request)
    {
        $model = new VolunteerSalary();

        $id = $request->volunteer_salary_code;

        $cek = $model->cekData($id);
        if ($cek == false) {
            return $this->responseError('Data volunteer salary tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $deleteResult = $model->deleteData($id);

            if ($deleteResult == false) {
                DB::rollBack();

                return $this->responseError('Gagal menghapus data volunteer salary', 500);
            }

            DB::commit();

            return $this->responseSuccess('Data volunteer salary berhasil dihapus', 200, [
                'volunteer_salary_code' => $id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function getListData(GetRequest $request)
    {
        $model = new VolunteerSalary();

        $params = [
            'dari' => $request->dari,
            'sampai' => $request->sampai,
            'search_keyword' => $request->search_keyword ?? '',
        ];

        $result = $model->getAllData($params);
        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }
}
