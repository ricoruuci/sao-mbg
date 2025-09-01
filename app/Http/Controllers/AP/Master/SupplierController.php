<?php

namespace App\Http\Controllers\AP\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AP\Master\APMsSupplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {

        $supplier = new APMsSupplier();

        $validator = Validator::make($request->all(), $supplier::$rulesInsert);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {

            $kodesupp = $supplier->beforeAutoNumber($request->input('suppname'));

            DB::beginTransaction();

            try {
                $insert = $supplier->insertData([
                    'suppid' => $kodesupp,
                    'suppname' => $request->input('suppname'),
                    'address' => $request->input('address'),
                    'city' => $request->input('city'),
                    'contactperson' => $request->input('contactperson'),
                    'phone' => $request->input('phone'),
                    'fax' => $request->input('fax'),
                    'email' => $request->input('email'),
                    'note' => $request->input('note'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);


                DB::commit();

                if ($insert) {
                    $result = [
                        'success' => true
                    ];
                } else {
                    return $this->responseError('insert gagal', 400);
                }

                return $result;
            } catch (\Exception $e) {
                DB::rollBack();

                return $this->responseError($e->getMessage(), 400);
            }
        }
    }

    public function getListData(Request $request)
    {
        $supplier = new APMsSupplier();

        if ($request->input('suppid')) {

            $result = $supplier->getdata(
                [
                    'suppid' => $request->input('suppid')
                ]
            );

            return $this->responseData($result);
        } else {
            $result = $supplier->getListData([
                'suppidkeyword' => $request->input('suppidkeyword') ?? '',
                'suppnamekeyword' => $request->input('suppnamekeyword') ?? '',
                'sortby' => $request->input('sortby') ?? 'suppid',
            ]);

            $resultPaginated = $this->arrayPaginator($request, $result);

            return $this->responsePagination($resultPaginated);
        }
    }


    public function updateAllData(Request $request, $suppid)
    {
        $supplier = new APMsSupplier();

        $validator = Validator::make($request->all(), $supplier::$rulesUpdateAll);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {

            $cek = $supplier->cekSupplier($suppid);

            if ($cek == false) {

                return $this->responseError('kode supplier tidak terdaftar dalam master', 400);
            }

            DB::beginTransaction();

            try {
                $updated = $supplier->updateAllData([
                    'suppid' => $suppid,
                    'suppname' => $request->input('suppname'),
                    'address' => $request->input('address'),
                    'city' => $request->input('city'),
                    'contactperson' => $request->input('contactperson'),
                    'phone' => $request->input('phone'),
                    'fax' => $request->input('fax'),
                    'email' => $request->input('email'),
                    'note' => $request->input('note'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);

                DB::commit();

                if ($updated) {
                    $result = [
                        'success' => true,
                        'updated' => $updated
                    ];
                } else {
                    return $this->responseError('update gagal', 400);
                }

                return $result;
            } catch (\Exception $e) {
                DB::rollBack();

                return $this->responseError($e->getMessage(), 400);
            }
        }
    }

    public function deleteData(Request $request, $suppid)
    {
        $supplier = new APMsSupplier();

        $cek = $supplier->cekSupplier($suppid);

        if ($cek == false) {

            return $this->responseError('kode supplier tidak terdaftar dalam master', 400);
        }

        DB::beginTransaction();

        try {
            $deleted = $supplier->deleteData([
                'suppid' => $suppid
            ]);

            DB::commit();

            if ($deleted) {
                $result = [
                    'success' => true,
                    'deleted' => $deleted
                ];
            } else {
                return $this->responseError('delete gagal', 400);
            }

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }
}
