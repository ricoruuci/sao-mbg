<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Http\Requests\Supplier\InsertRequest;
use App\Http\Requests\Supplier\UpdateRequest;
use App\Http\Requests\Supplier\DeleteRequest;
use App\Http\Requests\Supplier\GetRequest;
use App\Http\Requests\Supplier\GetRequestById;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getDataById(GetRequestById $request)
    {
        $supplier_model = new Supplier();

        $id = $request->supplier_id;

        $cek = $supplier_model->cekData($request->supplier_id);
        if ($cek == false) {
            return $this->responseError('Supplier tidak ditemukan', 404);
        }

        $result = $supplier_model->getDataById($id);

        return $this->responseData($result);
    }

    public function insertData(InsertRequest $request)
    {
        $model = new Supplier();

        DB::beginTransaction();
        
        try {
            $autonumber = $model->beforeAutoNumber();
            
            $params = [
                'supplier_id' => $autonumber,
                'supplier_name' => $request->supplier_name,
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
            ];

            $insertResult = $model->insertData($params);

            if ($insertResult == false) {
                return $this->responseError('Gagal menyimpan data supplier', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data supplier berhasil disimpan', 200, ['supplier_id' => $autonumber]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $model = new Supplier();

        $params = [
            'supplier_id' => $request->supplier_id,
            'supplier_name' => $request->supplier_name,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        $cek = $model->cekData($request->supplier_id);
        if ($cek == false) {
            return $this->responseError('Supplier tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $updateResult = $model->updateData($params);

            if ($updateResult == false) {
                return $this->responseError('Gagal memperbarui data supplier', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data supplier berhasil diperbarui', 200, ['supplier_id' => $request->supplier_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
    public function deleteData(DeleteRequest $request)
    {
        $model = new Supplier();

        $id = $request->supplier_id;

        $cek = $model->cekData($request->supplier_id);
        if ($cek == false) {
            return $this->responseError('Supplier tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $deleteResult = $model->deleteData($id);

            if ($deleteResult == false) {
                return $this->responseError('Gagal menghapus data supplier', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data supplier berhasil dihapus', 200, ['supplier_id' => $id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
    
    public function getListData(GetRequest $request)
    {
        $group_model = new Supplier();

        $params = [
            'search_keyword' => $request->search_keyword ?? '',
        ];

        $result = $group_model->getAllData($params);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>

