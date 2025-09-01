<?php

namespace App\Http\Controllers\IN\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IN\Master\INMsGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {

        $group = new INMsGroup();

        $validator = Validator::make($request->all(), $group::$rulesInsert);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {

            DB::beginTransaction();

            try {
                $insert = $group->insertData([
                    'groupid' => $request->input('groupid'),
                    'groupdesc' => $request->input('groupdesc'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                ]);

                if ($insert) {
                    $result = [
                        'success' => true
                    ];
                } else {
                    DB::rollBack();

                    return $this->responseError('insert gagal', 400);
                }

                DB::commit();

                return $result;
            } catch (\Exception $e) {
                DB::rollBack();

                return $this->responseError($e->getMessage(), 400);
            }
        }
    }

    public function updateAllData(Request $request)
    {

        $group = new INMsGroup();


        $validator = Validator::make($request->all(), $group::$rulesUpdateAll);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {
            $cek = $group->cekGroup($request->input('groupid'));

            if ($cek == false) {

                return $this->responseError('kode group tidak terdaftar dalam master', 400);
            }

            DB::beginTransaction();

            try {
                $updated = $group->updateAllData([
                    'groupid' => $request->input('groupid'),
                    'groupdesc' => $request->input('groupdesc'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                ]);

                if ($updated) {
                    $result = [
                        'success' => true,
                        'updated' => $updated
                    ];
                } else {
                    DB::rollBack();

                    return $this->responseError('update gagal', 400);
                }

                DB::commit();

                return $result;
            } catch (\Exception $e) {
                DB::rollBack();

                return $this->responseError($e->getMessage(), 400);
            }
        }
    }

    public function getListData(Request $request)
    {
        $group = new INMsGroup();

        $result = $group->getListData([
            'groupidkeyword' => $request->input('groupidkeyword'),
            'groupdesckeyword' => $request->input('groupdesckeyword')
        ]);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function deleteData(Request $request)
    {
        $group = new INMsGroup();

        $cek = $group->cekGroup($request->input('groupid'));

        if ($cek == false) {

            return $this->responseError('kode group tidak terdaftar dalam master', 400);
        }

        DB::beginTransaction();

        try {
            $deleted = $group->deleteData([
                'itemid' => $request->input('groupid'),
            ]);

            if ($deleted) {
                $result = [
                    'success' => true,
                    'deleted' => $deleted
                ];
            } else {
                DB::rollBack();

                return $this->responseError('delete gagal', 400);
            }

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }
}
