<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rekening;
use App\Models\GroupRekening;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Rekening\GetRequest;
use App\Http\Requests\Rekening\GetRequestById;  
use App\Http\Requests\Rekening\InsertRequest;
use App\Http\Requests\Rekening\UpdateRequest;
use App\Http\Requests\Rekening\DeleteRequest;

class RekeningController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getListData(GetRequest $request)
    {
        $model = new Rekening();

        $result = $model->getAllData([
            'search_keyword' => $request->input('search_keyword') ?? ''
        ]);
        
        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

    public function getDataById(GetRequestById $request)
    {
        $model = new Rekening();

        $id = $request->rekening_id;

        $cek = $model->cekData($id);
        if (is_null($cek)) {
            return $this->responseError('Data Rekening tidak ditemukan', 404);
        }

        $result = $model->getDataById($id);

        return $this->responseData($result);
    }

    public function updateData(UpdateRequest $request)
    {
        $model = new Rekening();
        $modelGroup = new GroupRekening();

        $id = $request->rekening_id;

        $cek = $model->cekData($id);
        if (is_null($cek)) {
            return $this->responseError('Data Rekening tidak ditemukan', 404);
        }

        $cek = $modelGroup->cekData($request->group_rek_id);
        if (is_null($cek)) {
            return $this->responseError('Data Group Rekening tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try 
        {
            $data = [
                'rekening_id' => $id,
                'rekening_name' => $request->input('rekening_name'),
                'group_rek_id' => $request->input('group_rek_id'),
                'note' => $request->input('note', ''),
                'upduser' => Auth::user()->currentAccessToken()['namauser']
            ];

            $result = $model->updateData($data);

            if ($result == false) {
                return $this->responseError('Gagal menyimpan data rekening', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data rekening berhasil disimpan', 200, ['rekening' => $request->rekening_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function insertData(InsertRequest $request)
    {
        $model = new Rekening();
        $modelGroup = new GroupRekening();

        $cek = $model->cekData($request->rekening_id);
        if ($cek) {
            return $this->responseError('Data rekening sudah ada', 404);
        }

        $cek = $modelGroup->cekData($request->group_rek_id);
        if ($cek==false) {
            return $this->responseError('Data Group Rekening tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try 
        {
            $data = [
                'rekening_id' => $request->rekening_id,
                'rekening_name' => $request->input('rekening_name'),
                'group_rek_id' => $request->input('group_rek_id'),
                'note' => $request->input('note', ''),
                'upduser' => Auth::user()->currentAccessToken()['namauser']
            ];

            $result = $model->insertData($data);

            if ($result == false) {
                return $this->responseError('Gagal menyimpan data rekening', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data rekening berhasil disimpan', 200, ['rekening' => $request->rekening_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function deleteData(DeleteRequest $request)
    {
        $model = new Rekening();

        $id = $request->rekening_id;

        $cek = $model->cekData($id);
        if (is_null($cek)) {
            return $this->responseError('Data rekening tidak ditemukan', 404);
        }

        $cek = $model->cekTerpakai($id);
        if ($cek) {
            return $this->responseError('Data rekening sudah pernah dipakai di transaksi, tidak bisa hapus', 400);
        }

        DB::beginTransaction();

        try 
        {
            $result = $model->deleteData($id);

            if ($result == false) {
                return $this->responseError('Gagal menghapus data rekening', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data rekening berhasil dihapus', 200, ['rekening' => $id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

}

?>
