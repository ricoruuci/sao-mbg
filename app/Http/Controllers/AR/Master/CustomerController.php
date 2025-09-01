<?php

namespace App\Http\Controllers\AR\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AR\Master\ARMsCustomer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {

        $customer = new ARMsCustomer();

        $validator = Validator::make($request->all(), $customer::$rulesInsert);

        if ($validator->fails())
        {
            return $this->responseError($validator->messages(), 400);
        }
        else
        {
    
            $kodecust = $customer->beforeAutoNumber($request->input('custtype'),$request->input('custname'));

            DB::beginTransaction();

            try 
            {
                $insert = $customer->insertData([
                    'custid' => $kodecust,
                    'custname' => $request->input('custname'),
                    'address' => $request->input('address'),
                    'city' => $request->input('city'),
                    'phone' => $request->input('phone'),
                    'email' => $request->input('email'),
                    'npwp' => $request->input('npwp'),
                    'note' => $request->input('note'),
                    'custtype' => $request->input('custtype'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'limitpiutang' => $request->input('limitpiutang'),
                    'limitasli' => $request->input('limitasli'),
                    'fgkoma' => $request->input('fgkoma'),
                    'up' => $request->input('up'),
                    'term' => $request->input('term'),
                    'salesid' => $request->input('salesid'),
                    'kodefp' => $request->input('kodefp')
                ]);

                if ($insert)
                {
                    $result = [
                        'success' => true
                    ];
                }
                else
                {
                    DB::rollBack();

                    return $this->responseError('insert gagal', 400);
                }

                DB::commit();
                
                return $result;

            } 
            catch (\Exception $e)
            {
                DB::rollBack();

                return $this->responseError($e->getMessage(), 400);
            }

        }

    }

    public function getListData(Request $request)
    {
        $customer = new ARMsCustomer();

        if ($request->input('custid')) {

            $cek = $customer->cekCustomer($request->input('custid'));

            if ($cek == false) {

                return $this->responseError('kode customer tidak terdaftar dalam master', 400);
            }

            $result = $customer->getdata(
                [
                    'custid' => $request->input('custid')
                ]
            );

            return $this->responseData($result);
        } else {
            $result = $customer->getListData([
                'custidkeyword' => $request->input('custidkeyword') ?? '',
                'custnamekeyword' => $request->input('custnamekeyword') ?? '',
                'sortby' => $request->input('sortby') ?? 'custid',
            ]);

            $resultPaginated = $this->arrayPaginator($request, $result);

            return $this->responsePagination($resultPaginated);
        }
    }

    public function getData(Request $request, $custid)
    {
        $customer = new ARMsCustomer();

        $result = $customer->getdata(
            [
                'custid' => $custid
            ]
        );

        return $this->responseData($result);

    }

    public function updateAllData(Request $request, $custid)
    {
        $customer = new ARMsCustomer();

        $validator = Validator::make($request->all(), $customer::$rulesUpdateAll);

        if ($validator->fails())
        {
            return $this->responseError($validator->messages(), 400);
        }
        else
        {
            
            $cek = $customer->cekCustomer($custid);

            if($cek==false){

                return $this->responseError('kode customer tidak terdaftar dalam master', 400);
            }

            
            DB::beginTransaction();

            try 
            {
                $updated = $customer->updateAllData([
                    'custid' => $custid,
                    'custname' => $request->input('custname'),
                    'address' => $request->input('address'),
                    'city' => $request->input('city'),
                    'phone' => $request->input('phone'),
                    'npwp' => $request->input('npwp'),
                    'email' => $request->input('email'),
                    'note' => $request->input('note'),
                    'custtype' => $request->input('custtype'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'limitpiutang' => $request->input('limitpiutang'),
                    'limitasli' => $request->input('limitasli'),
                    'fgkoma' => $request->input('fgkoma'),
                    'up' => $request->input('up'),
                    'term' => $request->input('term'),
                    'salesid' => $request->input('salesid'),
                    'kodefp' => $request->input('kodefp')                    
                ]);

                if ($updated)
                {
                    $result = [
                        'success' => true,
                        'updated' => $updated
                    ];
                }
                else
                {   
                    DB::rollBack();

                    return $this->responseError('update gagal', 400);
                }

                DB::commit();

                return $result;
            } 
            catch (\Exception $e)
            {
                DB::rollBack();

                return $this->responseError($e->getMessage(), 400);
            }

            
        }

    }
    
    public function deleteData(Request $request, $custid)
    {
        $customer = new ARMsCustomer();

        $cek = $customer->cekCustomer($custid);

        if($cek==false){

            return $this->responseError('kode customer tidak terdaftar dalam master', 400);
        }
        
        DB::beginTransaction();

        try 
        {
            $deleted = $customer->deleteData([
                'custid' => $custid
            ]);

            if ($deleted)
            {
                $result = [
                    'success' => true,
                    'deleted' => $deleted
                ];
            }
            else
            {
                DB::rollBack();

                return $this->responseError('delete gagal', 400);
            }

            DB::commit();

            return $result;
        } 
        catch (\Exception $e)
        {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }
}