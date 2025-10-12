<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroupMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Http\Requests\GroupMenu\InsertRequest;
use App\Http\Requests\GroupMenu\UpdateRequest;
use App\Http\Requests\GroupMenu\DeleteRequest;
use App\Http\Requests\GroupMenu\GetRequest;
use App\Http\Requests\GroupMenu\GetRequestById;
use Illuminate\Support\Facades\Auth;

class GroupMenuController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getDataById(GetRequestById $request)
    {
        $group_model = new GroupMenu();

        $id = $request->group_menu_id;

        $cek = $group_model->cekData($request->group_menu_id);
        if ($cek == false) {
            return $this->responseError('Menu tidak ditemukan', 404);
        }

        $result = $group_model->getDataById($id);

        return $this->responseData($result);
    }

    public function insertData(InsertRequest $request)
    {
        $model = new GroupMenu();

        DB::beginTransaction();
        
        try {
            $autonumber = $model->beforeAutoNumber();
            
            $params = [
                'group_menu_id' => $autonumber,
                'group_menu_name' => $request->group_menu_name,
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
            ];

            $insertResult = $model->insertData($params);

            if ($insertResult == false) {
                return $this->responseError('Gagal menyimpan data group menu', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data group menu berhasil disimpan', 200, ['group_menu_id' => $autonumber]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $model = new GroupMenu();

        $params = [
            'group_menu_id' => $request->group_menu_id,
            'group_menu_name' => $request->group_menu_name,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        $cek = $model->cekData($request->group_menu_id);
        if ($cek == false) {
            return $this->responseError('Group menu tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $updateResult = $model->updateData($params);

            if ($updateResult == false) {
                return $this->responseError('Gagal memperbarui data group menu', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data group menu berhasil diperbarui', 200, ['group_menu_id' => $request->group_menu_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
    public function deleteData(DeleteRequest $request)
    {
        $model = new GroupMenu();

        $id = $request->group_menu_id;

        $cek = $model->cekData($request->group_menu_id);
        if ($cek == false) {
            return $this->responseError('Group menu tidak ditemukan', 404);
        }

        $cek = $model->cekTerpakai($request->group_menu_id);
        if ($cek) {
            return $this->responseError('Group menu sudah terpakai, tidak bisa dihapus', 400);
        }

        DB::beginTransaction();

        try {
            $deleteResult = $model->deleteData($id);

            if ($deleteResult == false) {
                return $this->responseError('Gagal menghapus data group menu', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data group menu berhasil dihapus', 200, ['group_menu_id' => $id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
    
    public function getListData(GetRequest $request)
    {
        $group_model = new GroupMenu();

        $params = [
            'search_keyword' => $request->search_keyword ?? '',
        ];

        $result = $group_model->getAllData($params);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>

