<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroupBahanBaku;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Http\Requests\GroupBahanBaku\InsertRequest;
use App\Http\Requests\GroupBahanBaku\UpdateRequest;
use App\Http\Requests\GroupBahanBaku\DeleteRequest;
use App\Http\Requests\GroupBahanBaku\GetRequest;
use App\Http\Requests\GroupBahanBaku\GetRequestById;
use Illuminate\Support\Facades\Auth;

class GroupBahanBakuController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getDataById(GetRequestById $request)
    {
        $group_model = new GroupBahanBaku();

        $id = $request->group_bahan_baku_id;

        $cek = $group_model->cekData($request->group_bahan_baku_id);
        if ($cek == false) {
            return $this->responseError('Group Bahan Baku tidak ditemukan', 404);
        }

        $result = $group_model->getDataById($id);

        return $this->responseData($result);
    }

    public function insertData(InsertRequest $request)
    {
        $model = new GroupBahanBaku();

        DB::beginTransaction();
        
        try {
            $autonumber = $model->beforeAutoNumber();
            
            $params = [
                'group_bahan_baku_id' => $autonumber,
                'group_bahan_baku_name' => $request->group_bahan_baku_name,
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
            ];

            $insertResult = $model->insertData($params);

            if ($insertResult == false) {
                return $this->responseError('Gagal menyimpan data group bahan baku', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data group bahan baku berhasil disimpan', 200, ['group_bahan_baku_id' => $autonumber]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $model = new GroupBahanBaku();

        $params = [
            'group_bahan_baku_id' => $request->group_bahan_baku_id,
            'group_bahan_baku_name' => $request->group_bahan_baku_name,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        $cek = $model->cekData($request->group_bahan_baku_id);
        if ($cek == false) {
            return $this->responseError('Group bahan baku tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $updateResult = $model->updateData($params);

            if ($updateResult == false) {
                return $this->responseError('Gagal memperbarui data group bahan baku', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data group bahan baku berhasil diperbarui', 200, ['group_bahan_baku_id' => $request->group_bahan_baku_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
    public function deleteData(DeleteRequest $request)
    {
        $model = new GroupBahanBaku();

        $id = $request->group_bahan_baku_id;

        $cek = $model->cekData($request->group_bahan_baku_id);
        if ($cek == false) {
            return $this->responseError('Group bahan baku tidak ditemukan', 404);
        }

        $cek = $model->cekTerpakai($request->group_bahan_baku_id);
        if ($cek) {
            return $this->responseError('Group bahan baku sudah terpakai, tidak bisa dihapus', 400);
        }

        DB::beginTransaction();

        try {
            $deleteResult = $model->deleteData($id);

            if ($deleteResult == false) {
                return $this->responseError('Gagal menghapus data group bahan baku', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data group bahan baku berhasil dihapus', 200, ['group_bahan_baku_id' => $id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
    
    public function getListData(GetRequest $request)
    {
        $group_model = new GroupBahanBaku();

        $params = [
            'search_keyword' => $request->search_keyword ?? '',
        ];

        $result = $group_model->getAllData($params);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>

