<?php

namespace App\Http\Controllers\CF\Master; //ini cek foldernya

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CF\Master\CFMsRekening; //cek nama model nya
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class RekeningController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {

        $customer = new CFMsRekening();

        $validator = Validator::make($request->all(), $customer::$rulesInsert, $customer::$messagesInsert);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {

            $kodecust = $customer->beforeAutoNumber($request->input('grouprekid'));

            DB::beginTransaction();

            try {
                $insert = $customer->insertData([
                    'rekeningid' => $kodecust,
                    'rekeningname' => $request->input('rekeningname'),
                    'grouprekid' => $request->input('grouprekid'),
                    'tipe' => $request->input('tipe'),
                    'fgtipe' => $request->input('fgtipe'),
                    'fgactive' => $request->input('fgactive') ?? 'Y',
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);

                if ($insert) {
                    DB::commit();

                    return $this->responseSuccess('insert berhasil', 200, ['rekeningid' => $kodecust]);
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

    public function getListData(Request $request)
    {
        $sales = new CFMsRekening();

        if ($request->input('rekeningid')) {

            $cek = $sales->cekRekening($request->input('rekeningid'));

            if ($cek == false) {

                return $this->responseError('kode rekening tidak terdaftar dalam master', 400);
            }

            $result = $sales->getdata(
                [
                    'rekeningid' => $request->input('rekeningid')
                ]
            );

            return $this->responseData($result);
        } else {

            $result = $sales->getListData([
                'rekeningidkeyword' => $request->input('rekeningidkeyword') ?? '',
                'rekeningnamekeyword' => $request->input('rekeningnamekeyword') ?? '',
                'grouprekidkeyword' => $request->input('grouprekidkeyword') ?? '',
                'groupreknamekeyword' => $request->input('groupreknamekeyword') ?? ''
            ]);

            $resultPaginated = $this->arrayPaginator($request, $result);

            return
                $this->responsePagination($resultPaginated);
        }
    }
    public function getData(Request $request, $rekeningid)
    {
        $rek = new CFMsRekening();

        $result = $rek->getdata(
            [
                'rekeningid' => $rekeningid
            ]
        );

        return $this->responseData($result);
    }

    public function updateAllData(Request $request)
    {
        $customer = new CFMsRekening();

        $validator = Validator::make($request->all(), $customer::$rulesUpdateAll, $customer::$messagesUpdate);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {

            $cek = $customer->cekRekening($request->input('rekeningid'));

            if ($cek == false) {

                return $this->responseError('kode rekening tidak terdaftar dalam master', 400);
            }


            DB::beginTransaction();

            try {
                $updated = $customer->updateAllData([
                    'rekeningid' => $request->input('rekeningid'),
                    'rekeningname' => $request->input('rekeningname'),
                    'grouprekid' => $request->input('grouprekid'),
                    'tipe' => $request->input('tipe'),
                    'fgtipe' => $request->input('fgtipe'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);

                if ($updated) {

                    DB::commit();

                    return $this->responseSuccess('update berhasil', 200, ['rekeningid' => $request->input('rekeningid')]);
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

    public function deleteData(Request $request)
    {
        $customer = new CFMsRekening();

        $cek = $customer->cekRekening($request->input('rekeningid'));

        if ($cek == false) {

            return $this->responseError('kode rekening tidak terdaftar dalam master', 400);
        }

        DB::beginTransaction();

        try {
            $deleted = $customer->deleteData([
                'rekeningid' => $request->input('rekeningid')
            ]);

            if ($deleted) {
                DB::commit();

                return $this->responseSuccess('delete berhasil', 200, ['rekeningid' => $request->input('rekeningid')]);
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
