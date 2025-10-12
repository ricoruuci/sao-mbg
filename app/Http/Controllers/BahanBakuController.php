<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Http\Requests\BahanBaku\InsertRequest;
use App\Http\Requests\BahanBaku\UpdateRequest;
use App\Http\Requests\BahanBaku\DeleteRequest;
use App\Http\Requests\BahanBaku\GetRequest;
use App\Http\Requests\BahanBaku\GetRequestById;
use Illuminate\Support\Facades\Auth;

class BahanBakuController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getDataById(GetRequestById $request)
    {
        $bahan_baku_model = new BahanBaku();

        $id = $request->bahan_baku_id;

        $cek = $bahan_baku_model->cekData($request->bahan_baku_id);
        if ($cek == false) {
            return $this->responseError('Bahan baku tidak ditemukan', 404);
        }

        $result = $bahan_baku_model->getDataById($id);

        return $this->responseData($result);
    }

    public function insertData(InsertRequest $request)
    {
        $model = new BahanBaku();

        DB::beginTransaction();
        
        try {
            $autonumber = $model->beforeAutoNumber();
            
            $params = [
                'bahan_baku_id' => $autonumber,
                'bahan_baku_name' => $request->bahan_baku_name,
                'satuan' => $request->satuan,
                'group_bahan_baku_id' => $request->group_bahan_baku_id
            ];

            $insertResult = $model->insertData($params);

            if ($insertResult == false) {
                return $this->responseError('Gagal menyimpan data bahan baku', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data bahan baku berhasil disimpan', 200, ['bahan_baku_id' => $autonumber]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $model = new BahanBaku();

        $params = [
            'bahan_baku_id' => $request->bahan_baku_id,
            'bahan_baku_name' => $request->bahan_baku_name,
            'satuan' => $request->satuan,
            'group_bahan_baku_id' => $request->group_bahan_baku_id
        ];

        $cek = $model->cekData($request->bahan_baku_id);
        if ($cek == false) {
            return $this->responseError('Bahan baku tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $updateResult = $model->updateData($params);

            if ($updateResult == false) {
                return $this->responseError('Gagal memperbarui data bahan baku', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data bahan baku berhasil diperbarui', 200, ['bahan_baku_id' => $request->bahan_baku_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
    public function deleteData(DeleteRequest $request)
    {
        $model = new BahanBaku();

        $id = $request->bahan_baku_id;

        $cek = $model->cekData($request->bahan_baku_id);
        if ($cek == false) {
            return $this->responseError('Bahan baku tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $deleteResult = $model->deleteData($id);

            if ($deleteResult == false) {
                return $this->responseError('Gagal menghapus data bahan baku', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data bahan baku berhasil dihapus', 200, ['bahan_baku_id' => $id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
    
    public function getListData(GetRequest $request)
    {
        $bahan_baku_model = new BahanBaku();

        $params = [
            'search_keyword' => $request->search_keyword ?? '',
        ];

        $result = $bahan_baku_model->getAllData($params);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>

