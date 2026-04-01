<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\DeleteRequest;
use App\Http\Requests\Customer\GetRequest;
use App\Http\Requests\Customer\GetRequestById;
use App\Http\Requests\Customer\InsertRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Models\Customer;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getDataById(GetRequestById $request)
    {
        $customerModel = new Customer();

        $id = $request->customer_id;

        $cek = $customerModel->cekData($id);
        if ($cek == false) {
            return $this->responseError('Customer tidak ditemukan', 404);
        }

        $result = $customerModel->getDataById($id);

        return $this->responseData($result);
    }

    public function getListData(GetRequest $request)
    {
        $customerModel = new Customer();

        $params = [
            'search_keyword' => $request->search_keyword ?? '',
        ];

        $result = $customerModel->getAllData($params);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function insertData(InsertRequest $request)
    {
        $model = new Customer();

        DB::beginTransaction();

        try {
            $autonumber = $model->beforeAutoNumber();

            $params = [
                'customer_id' => $autonumber,
                'customer_name' => $request->customer_name,
                'customer_contact_person' => $request->customer_contact_person,
                'customer_city' => $request->customer_city,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'customer_npwp' => $request->customer_npwp,
                'customer_account_manager' => $request->customer_account_manager,
                'customer_limit_piutang' => $request->customer_limit_piutang ?? 0,
                'customer_address' => $request->customer_address,
                'customer_address_npwp' => $request->customer_address_npwp,
                'customer_note' => $request->customer_note,
                'customer_term' => $request->customer_term ?? 0,
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
            ];

            $insertResult = $model->insertData($params);

            if ($insertResult == false) {
                return $this->responseError('Gagal menyimpan data customer', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data customer berhasil disimpan', 200, ['customer_id' => $autonumber]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $model = new Customer();

        $params = [
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'customer_contact_person' => $request->customer_contact_person,
            'customer_city' => $request->customer_city,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'customer_npwp' => $request->customer_npwp,
            'customer_account_manager' => $request->customer_account_manager,
            'customer_limit_piutang' => $request->customer_limit_piutang ?? 0,
            'customer_address' => $request->customer_address,
            'customer_address_npwp' => $request->customer_address_npwp,
            'customer_note' => $request->customer_note,
            'customer_term' => $request->customer_term ?? 0,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        $cek = $model->cekData($request->customer_id);
        if ($cek == false) {
            return $this->responseError('Customer tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $updateResult = $model->updateData($params);

            if ($updateResult == false) {
                return $this->responseError('Gagal memperbarui data customer', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data customer berhasil diperbarui', 200, ['customer_id' => $request->customer_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function deleteData(DeleteRequest $request)
    {
        $model = new Customer();

        $id = $request->customer_id;

        $cek = $model->cekData($id);
        if ($cek == false) {
            return $this->responseError('Customer tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $deleteResult = $model->deleteData($id);

            if ($deleteResult == false) {
                return $this->responseError('Gagal menghapus data customer', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data customer berhasil dihapus', 200, ['customer_id' => $id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}
