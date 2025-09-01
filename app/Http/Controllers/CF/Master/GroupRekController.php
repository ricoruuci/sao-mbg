<?php

namespace App\Http\Controllers\CF\Master; //ini cek foldernya

use App\Http\Controllers\Controller;
use App\Models\CF\Master\MsGroupRek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class GroupRekController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {

        $customer = new MsGroupRek();

        $validator = Validator::make($request->all(), $customer::$rulesInsert, $customer::$messagesInsert);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {

            $kodecust = $customer->beforeAutoNumber($request->input('kode'));

            DB::beginTransaction();

            try {
                $insert = $customer->insertData([
                    'grouprekid' => $kodecust,
                    'grouprekname' => $request->input('grouprekname'),
                    'kode' => $request->input('kode'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);

                if ($insert) {
                    DB::commit();

                    return $this->responseSuccess('insert berhasil', 200, ['grouprekid' => $kodecust]);
                } else {
                    DB::rollBack();

                    return $this->responseError('insert gagal', 400);
                }
            } catch (\Exception $e) {
                DB::rollBack();

                return $this->responseError($e->getMessage(), 400);
            }
        }
    }

    public function updateAllData(Request $request)
    {
        $customer = new MsGroupRek();

        $validator = Validator::make($request->all(), $customer::$rulesUpdateAll, $customer::$messagesUpdate);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {

            $cek = $customer->cekGroupRek($request->input('grouprekid'));

            if ($cek == false) {

                return $this->responseError('kode group rekening tidak terdaftar dalam master', 400);
            }


            DB::beginTransaction();

            try {
                $updated = $customer->updateAllData([
                    'grouprekid' => $request->input('grouprekid'),
                    'grouprekname' => $request->input('grouprekname'),
                    'kode' => $request->input('kode'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);

                if ($updated) {

                    DB::commit();

                    return $this->responseSuccess('update berhasil', 200, ['grouprekid' => $request->input('grouprekid')]);
                } else {
                    DB::rollBack();

                    return $this->responseError('update gagal', 400);
                }
            } catch (\Exception $e) {
                DB::rollBack();

                return $this->responseError($e->getMessage(), 400);
            }
        }
    }

    public function getListData(Request $request)
    {
        $customer = new MsGroupRek();

        if ($request->input('grouprekid')) {

            $cek = $customer->cekGroupRek($request->input('grouprekid'));

            if ($cek == false) {

                return $this->responseError('kode group rekening tidak terdaftar dalam master', 400);
            }

            $result = $customer->getdata(
                [
                    'grouprekid' => $request->input('grouprekid')
                ]
            );

            return $this->responseData($result);
        } else {
            $result = $customer->getListData([
                'grouprekidkeyword' => $request->input('grouprekidkeyword') ?? '',
                'jeniskeyword' => $request->input('jeniskeyword') ?? 'all',
                'groupreknamekeyword' => $request->input('groupreknamekeyword') ?? '',
                'sortby' => $request->input('sortby') ?? 'id'
            ]);

            $resultPaginated = $this->arrayPaginator($request, $result);

            return $this->responsePagination($resultPaginated);
        }
    }

    public function deleteData(Request $request)
    {
        $customer = new MsGroupRek();

        $cek = $customer->cekGroupRek($request->input('grouprekid'));

        if ($cek == false) {

            return $this->responseError('kode group rekening tidak terdaftar dalam master', 400);
        }

        DB::beginTransaction();

        try {
            $deleted = $customer->deleteData([
                'grouprekid' => $request->input('grouprekid')
            ]);

            if ($deleted) {
                DB::commit();

                return $this->responseSuccess('delete berhasil', 200, ['grouprekid' => $request->input('grouprekid')]);
            } else {
                DB::rollBack();

                return $this->responseError('delete gagal', 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }
}
