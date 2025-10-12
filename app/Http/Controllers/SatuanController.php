<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Satuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Http\Requests\Satuan\InsertRequest;
use App\Http\Requests\Satuan\DeleteRequest;
use App\Http\Requests\Satuan\GetRequest;
use App\Http\Requests\Satuan\GetRequestById;
use Illuminate\Support\Facades\Auth;

class SatuanController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getDataById(GetRequestById $request)
    {
        $satuan_model = new Satuan();

        $id = $request->satuan;

        $cek = $satuan_model->cekData($request->satuan);
        if ($cek == false) {
            return $this->responseError('Satuan tidak ditemukan', 404);
        }

        $result = $satuan_model->getDataById($id);

        return $this->responseData($result);
    }

    public function insertData(InsertRequest $request)
    {
        $model = new Satuan();

        DB::beginTransaction();
        
        try { 
            $params = [
                'satuan' => $request->satuan
            ];

            $insertResult = $model->insertData($params);

            if ($insertResult == false) {
                return $this->responseError('Gagal menyimpan data satuan', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data satuan berhasil disimpan', 200, ['satuan' => $request->satuan]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function deleteData(DeleteRequest $request)
    {
        $model = new Satuan();

        $id = $request->satuan;

        $cek = $model->cekData($request->satuan);
        if ($cek == false) {
            return $this->responseError('Satuan tidak ditemukan', 404);
        }

        $cek = $model->cekTerpakai($request->satuan);
        if ($cek != false) {
            return $this->responseError('Satuan sedang digunakan', 400);
        }

        DB::beginTransaction();

        try {
            $deleteResult = $model->deleteData($id);

            if ($deleteResult == false) {
                return $this->responseError('Gagal menghapus data satuan', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data satuan berhasil dihapus', 200, ['satuan' => $id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
    
    public function getListData(GetRequest $request)
    {
        $group_model = new Satuan();

        $params = [
            'search_keyword' => $request->search_keyword ?? '',
        ];

        $result = $group_model->getAllData($params);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>

