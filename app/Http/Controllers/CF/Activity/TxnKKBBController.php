<?php

namespace App\Http\Controllers\CF\Activity; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CF\Activity\CFTrKKBBDt;
use App\Models\CF\Activity\CFTrKKBBHd;
use App\Models\AR\Master\ARMsCustomer;
use App\Models\AP\Master\APMsSupplier;
use App\Models\CF\Master\CFMsBank;
use App\Models\CF\Master\CFMsRekening;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class TxnKKBBController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {
        $cfheader = new CFTrKKBBHd();
        $cfdetail = new CFTrKKBBDt();
        $mssupplier = new APMsSupplier();
        $mscustomer = new ARMsCustomer();
        $msrekening = new CFMsRekening();
        $msbank = new CFMsBank();

        $validator = Validator::make($request->all(), $cfheader::$rulesInsert);

        if ($validator->fails())
        {
            return $this->responseError($validator->messages(), 400);
        }
        
        $validatorDetail = Validator::make($request->all(), $cfdetail::$rulesInsert, $cfdetail::$messagesInsert);

        if ($validatorDetail->fails())
        {
            return $this->responseError($validatorDetail->messages(), 400);
        }
        
        if ($request->input('fgtrans')=='ARK' or $request->input('fgtrans')=='ARB' or $request->input('fgtrans')=='ARC'){

            $cek = $mscustomer->cekCustomer($request->input('actor'));

            if($cek==false){

                return $this->responseError('kode pelanggan tidak terdaftar dalam master', 400);
            }

        }

        if ($request->input('fgtrans')=='APK' or $request->input('fgtrans')=='APB' or $request->input('fgtrans')=='APC'){

            $cek = $mssupplier->cekSupplier($request->input('actor'));

            if($cek==false){

                return $this->responseError('kode supplier tidak terdaftar dalam master', 400);
            }
        
        }

        if ($request->input('fgtrans')=='KK' or $request->input('fgtrans')=='KM' or $request->input('fgtrans')=='BK' or $request->input('fgtrans')=='BM'){

            if($request->input('actor')==false){

                return $this->responseError('nama kepada belum diisi', 400);
            }
        
        }

        if ($request->input('fgtrans')=='ARB' or $request->input('fgtrans')=='APB' or $request->input('fgtrans')=='BK' or $request->input('fgtrans')=='BM'){

            $cek = $msbank->cekBank($request->input('bankid'));
            
            if($cek==false){

                return $this->responseError('kode bank tidak terdaftar dalam master', 400);
            }
        
        }

        $arrDetail = $request->input('detail');

        $sum = 0.00;

        for ($i = 0; $i < sizeof($arrDetail); $i++)
        {
            if ($request->input('fgtrans')=='KK' or $request->input('fgtrans')=='BK' or $request->input('fgtrans')=='APB' 
             or $request->input('fgtrans')=='APC' or $request->input('fgtrans')=='APK'){

                if ($arrDetail[$i]['jenis']=='D') {
                    $sum = $sum + $arrDetail[$i]['amount'];
                }
                else
                {   
                    $sum = $sum - $arrDetail[$i]['amount'];
                }
            }
            else
            {
                if ($arrDetail[$i]['jenis']=='K') {
                    $sum = $sum + $arrDetail[$i]['amount'];
                }
                else
                {   
                    $sum = $sum - $arrDetail[$i]['amount'];
                }
            }
            
        }
        
        if ($sum<>$request->input('total')){

            return $this->responseError('data yang diinput belum balance', 400);

        }

        DB::beginTransaction();

        try 
        {
            $hasilvoucherid = $cfheader->beforeAutoNumber($request->input('fgtrans'), $request->input('transdate1'));

            $insertheader = $cfheader->insertData([
                'voucherid' => $hasilvoucherid,
                'transdate' => $request->input('transdate'),
                'actor' => $request->input('actor'),
                'bankid' => $request->input('bankid'),
                'note' => $request->input('note'),
                'flagkkbb' => $request->input('fgtrans'),
                'currid' => $request->input('currid'),
                'total' => $request->input('total'),
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
                'nobgcek' => $request->input('nobgcek'),
                'transdate1' => $request->input('transdate1')
            ]);

            if ($insertheader)
            {
                $result = [
                    'success' => true
                ];
            }
            else
            {   
                DB::rollBack();

                return $this->responseError('insert header gagal', 400);
            }
            
            for ($i = 0; $i < sizeof($arrDetail); $i++)
            {

                $cek = $msrekening->cekRekening($arrDetail[$i]['rekeningid']);

                if($cek==false){

                    DB::rollBack();
                    
                    return $this->responseError('kode rekening tidak terdaftar dalam master', 400);
                }
                
                $insertdetail = $cfdetail->insertData([
                    'voucherid' => $hasilvoucherid,
                    'rekeningid' => $arrDetail[$i]['rekeningid'],
                    'note' => $arrDetail[$i]['note'],
                    'amount' => $arrDetail[$i]['amount'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'jenis' => $arrDetail[$i]['jenis']
                ]);

                if ($insertdetail)
                {
                    $result = [
                        'success' => true
                    ];
                }
                else
                {   
                    DB::rollBack();

                    return $this->responseError('insert detail gagal', 400);
                }

            }                

            $result = [
                'success' => true
            ];

            DB::commit();

            return $result;

        } 
        catch (\Exception $e)
        {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function getListData(Request $request)
    {
        $cfheader = new CFTrKKBBHd();
        $cfdetail = new CFTrKKBBDt();

        if ($request->input('voucherid'))
        {

            $resultheader = $cfheader->getdata(
                [
                    'voucherid' => $request->input('voucherid')
                ]
            );

            $resultdetail = $cfdetail->getdata(
                [
                    'voucherid' => $request->input('voucherid')
                ]
            );

            $result = [
                'header' => $resultheader,
                'detail' => $resultdetail
            ];

            return $this->responseData($result);    
        
        }
        else {
                  
            $result = $cfheader->getListData(
                [
                    'dari' => $request->input('dari'),
                    'sampai' => $request->input('sampai'),
                    'fgtrans' => $request->input('fgtrans'),
                    'bankid' => $request->input('bankid') ?? '',
                    'actorkeyword' => $request->input('actorkeyword') ?? '',
                    'voucherkeyword' => $request->input('voucherkeyword') ?? '',
                    'sortby' => $request->input('sortby') ?? 'old'
                ]
            );

            $resultPaginated = $this->arrayPaginator($request, $result);

            return $this->responsePagination($resultPaginated);
            

        } 

    }

    public function updateAllData(Request $request)
    {
        $cfheader = new CFTrKKBBHd();
        $cfdetail = new CFTrKKBBDt();
        $mssupplier = new APMsSupplier();
        $mscustomer = new ARMsCustomer();
        $msrekening = new CFMsRekening();
        $msbank = new CFMsBank();

        $validator = Validator::make($request->all(), $cfheader::$rulesUpdateAll);

        if ($validator->fails())
        {
            return $this->responseError($validator->messages(), 400);
        }
        
        $validatorDetail = Validator::make($request->all(), $cfdetail::$rulesInsert, $cfdetail::$messagesInsert);

        if ($validatorDetail->fails())
        {
            return $this->responseError($validatorDetail->messages(), 400);
        }
        
        $cek = $cfheader->cekVoucher($request->input('voucherid'));
            
        if($cek==false){

            return $this->responseError('nomor voucher tidak terdaftar', 400);
        } 

        if ($request->input('fgtrans')=='ARK' or $request->input('fgtrans')=='ARB' or $request->input('fgtrans')=='ARC'){

            $cek = $mscustomer->cekCustomer($request->input('actor'));

            if($cek==false){

                return $this->responseError('kode pelanggan tidak terdaftar dalam master', 400);
            }

        }

        if ($request->input('fgtrans')=='APK' or $request->input('fgtrans')=='APB' or $request->input('fgtrans')=='APC'){

            $cek = $mssupplier->cekSupplier($request->input('actor'));

            if($cek==false){

                return $this->responseError('kode supplier tidak terdaftar dalam master', 400);
            }
        
        }

        if ($request->input('fgtrans')=='KK' or $request->input('fgtrans')=='KM' or $request->input('fgtrans')=='BK' or $request->input('fgtrans')=='BM'){

            if($request->input('actor')==false){

                return $this->responseError('nama kepada belum diisi', 400);
            }
        
        }

        if ($request->input('fgtrans')=='ARB' or $request->input('fgtrans')=='APB' or $request->input('fgtrans')=='BK' or $request->input('fgtrans')=='BM'){

            $cek = $msbank->cekBank($request->input('bankid'));
            
            if($cek==false){

                return $this->responseError('kode bank tidak terdaftar dalam master', 400);
            }
        
        }

        $arrDetail = $request->input('detail');

        $sum = 0.00;

        for ($i = 0; $i < sizeof($arrDetail); $i++)
        {
            if ($request->input('fgtrans')=='KK' or $request->input('fgtrans')=='BK' or $request->input('fgtrans')=='APB' 
             or $request->input('fgtrans')=='APC' or $request->input('fgtrans')=='APK'){

                if ($arrDetail[$i]['jenis']=='D') {
                    $sum = $sum + $arrDetail[$i]['amount'];
                }
                else
                {   
                    $sum = $sum - $arrDetail[$i]['amount'];
                }
            }
            else
            {
                if ($arrDetail[$i]['jenis']=='K') {
                    $sum = $sum + $arrDetail[$i]['amount'];
                }
                else
                {   
                    $sum = $sum - $arrDetail[$i]['amount'];
                }
            }
            
        }
        
        if ($sum<>$request->input('total')){

            return $this->responseError('data yang diinput belum balance', 400);

        }

        DB::beginTransaction();

        try 
        {
            $insertheader = $cfheader->updateAllData([
                'voucherid' => $request->input('voucherid'),
                'transdate' => $request->input('transdate'),
                'actor' => $request->input('actor'),
                'bankid' => $request->input('bankid'),
                'note' => $request->input('note'),
                'currid' => $request->input('currid'),
                'total' => $request->input('total'),
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
                'nobgcek' => $request->input('nobgcek'),
                'transdate1' => $request->input('transdate1')
            ]);

            if ($insertheader)
            {
                $result = [
                    'success' => true
                ];
            }
            else
            {   
                DB::rollBack();

                return $this->responseError('update header gagal', 400);
            }

            $deletedetail = $cfdetail->deleteData([
                'voucherid' => $request->input('voucherid')
            ]);
            
            for ($i = 0; $i < sizeof($arrDetail); $i++)
            {

                $cek = $msrekening->cekRekening($arrDetail[$i]['rekeningid']);

                if($cek==false){

                    DB::rollBack();
                    
                    return $this->responseError('kode rekening tidak terdaftar dalam master', 400);
                }
                
                $insertdetail = $cfdetail->insertData([
                    'voucherid' => $request->input('voucherid'),
                    'rekeningid' => $arrDetail[$i]['rekeningid'],
                    'note' => $arrDetail[$i]['note'],
                    'amount' => $arrDetail[$i]['amount'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'jenis' => $arrDetail[$i]['jenis']
                ]);

                if ($insertdetail)
                {
                    $result = [
                        'success' => true
                    ];
                }
                else
                {   
                    DB::rollBack();

                    return $this->responseError('update detail gagal', 400);
                }

            }                

            $result = [
                'success' => true
            ];

            DB::commit();

            return $result;

        } 
        catch (\Exception $e)
        {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function deleteData(Request $request)
    {
        $cfheader = new CFTrKKBBHd();

        $cek = $cfheader->cekVoucher($request->input('voucherid'));
            
        if($cek==false){

            return $this->responseError('nomor voucher tidak terdaftar', 400);
        } 

        DB::beginTransaction();

        try 
        {
            $deleted = $cfheader->deleteData([
                'voucherid' => $request->input('voucherid')
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

    public function cariNota(Request $request)
    {
        $cfdetail = new CFTrKKBBDt();
        $mssupplier = new APMsSupplier();
        $mscustomer = new ARMsCustomer();

        $validator = Validator::make($request->all(), $cfdetail::$rulesCariNota);

        if ($validator->fails())
        {
            return $this->responseError($validator->messages(), 400);
        }

        if ($request->input('fgtrans')=='ARK' or $request->input('fgtrans')=='ARB' or $request->input('fgtrans')=='ARC'){

            $cek = $mscustomer->cekCustomer($request->input('actor'));

            if($cek==false){

                return $this->responseError('kode pelanggan tidak terdaftar dalam master', 400);
            }

        }

        if ($request->input('fgtrans')=='APK' or $request->input('fgtrans')=='APB' or $request->input('fgtrans')=='APC'){

            $cek = $mssupplier->cekSupplier($request->input('actor'));

            if($cek==false){

                return $this->responseError('kode supplier tidak terdaftar dalam master', 400);
            }
        
        }

        $result = $cfdetail->cariInvoiceBlmLunas(
            [
                'transdate' => $request->input('transdate'),
                'actor' => $request->input('actor'),
                'fgtrans' => $request->input('fgtrans')
            ]
        );

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>