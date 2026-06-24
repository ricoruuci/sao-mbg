<?php

namespace App\Http\Controllers;

use App\Http\Requests\Yayasan\DeleteRequest;
use App\Http\Requests\Yayasan\GetRequest;
use App\Http\Requests\Yayasan\GetRequestById;
use App\Http\Requests\Yayasan\InsertRequest;
use App\Http\Requests\Yayasan\UpdateRequest;
use App\Models\Yayasan;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class YayasanController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getDataById(GetRequestById $request)
    {
        $yayasanModel = new Yayasan();

        $id = $request->yayasan_code;

        $cek = $yayasanModel->cekData($id);
        if ($cek == false) {
            return $this->responseError('Yayasan tidak ditemukan', 404);
        }

        $result = $yayasanModel->getDataById($id);

        return $this->responseData($result);
    }

    public function getListData(GetRequest $request)
    {
        $yayasanModel = new Yayasan();

        $params = [
            'search_keyword' => $request->search_keyword ?? '',
        ];

        $result = $yayasanModel->getAllData($params);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function insertData(InsertRequest $request)
    {
        $model = new Yayasan();

        DB::beginTransaction();

        try {
            $autonumber = $model->beforeAutoNumber();

            $params = [
                'yayasan_code' => $autonumber,
                'yayasan_name' => $request->yayasan_name,
                'yayasan_address' => $request->yayasan_address,
                'yayasan_note' => $request->yayasan_note,
                'yayasan_phone' => $request->yayasan_phone,
                'yayasan_email' => $request->yayasan_email,
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
            ];

            $insertResult = $model->insertData($params);

            if ($insertResult == false) {
                return $this->responseError('Gagal menyimpan data yayasan', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data yayasan berhasil disimpan', 200, ['yayasan_code' => $autonumber]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $model = new Yayasan();

        $params = [
            'yayasan_code' => $request->yayasan_code,
            'yayasan_name' => $request->yayasan_name,
            'yayasan_address' => $request->yayasan_address,
            'yayasan_note' => $request->yayasan_note,
            'yayasan_phone' => $request->yayasan_phone,
            'yayasan_email' => $request->yayasan_email,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        $cek = $model->cekData($request->yayasan_code);
        if ($cek == false) {
            return $this->responseError('Yayasan tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $updateResult = $model->updateData($params);

            if ($updateResult == false) {
                return $this->responseError('Gagal memperbarui data yayasan', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data yayasan berhasil diperbarui', 200, ['yayasan_code' => $request->yayasan_code]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function deleteData(DeleteRequest $request)
    {
        $model = new Yayasan();

        $id = $request->yayasan_code;

        $cek = $model->cekData($id);
        if ($cek == false) {
            return $this->responseError('Yayasan tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $deleteResult = $model->deleteData($id);

            if ($deleteResult == false) {
                return $this->responseError('Gagal menghapus data yayasan', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data yayasan berhasil dihapus', 200, ['yayasan_code' => $id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}
