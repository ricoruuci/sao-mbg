<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CFTrKKBBDt;
use App\Models\CFTrKKBBHd;
use App\Models\Supplier;
use App\Models\Bank;
use App\Models\Rekening;
use App\Models\User;
use App\Models\AllFoto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TxnKKBB\UpdateRequest;
use App\Http\Requests\TxnKKBB\InsertRequest;
use App\Http\Requests\TxnKKBB\DeleteRequest;
use App\Http\Requests\TxnKKBB\GetRequest;
use App\Http\Requests\TxnKKBB\GetNotaRequest;
use App\Http\Requests\TxnKKBB\GetRequestById;

class TxnKKBBController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(InsertRequest $request)
    {
        $cfheader = new CFTrKKBBHd();
        $cfdetail = new CFTrKKBBDt();
        $msrekening = new Rekening();
        $msbank = new Bank();
        $mssupplier = new Supplier();
        $model_allfoto = new AllFoto();

        if ($request->input('flagkkbb')=='JU' and $request->input('total')<>0){
            return $this->responseError('Transaksi Jurnal Umum harus memiliki total 0', 400);
        }

        if ($request->input('flagkkbb')=='APB' or $request->input('flagkkbb')=='BK' or $request->input('flagkkbb')=='BM'){
            $cek = $msbank->cekData($request->input('bank_id'));
            if($cek==false){
                return $this->responseError('kode bank tidak terdaftar dalam master', 400);
            }
        }

        if ($request->input('flagkkbb')=='APB' or $request->input('flagkkbb')=='APK'){
            $cek = $mssupplier->cekData($request->input('actor'));
            if($cek==false){
                return $this->responseError('kode supplier tidak terdaftar dalam master', 400);
            }
        }

        $arrDetail = $request->input('detail');
        $sum = 0.00;
        for ($i = 0; $i < sizeof($arrDetail); $i++)
        {
            $cek = $msrekening->cekData($arrDetail[$i]['rekeningid']);
            if($cek==false){
                return $this->responseError('kode rekening tidak terdaftar dalam master', 400);
            }

            if ($request->input('flagkkbb')=='KK' or $request->input('flagkkbb')=='BK' or $request->input('flagkkbb')=='APB' or $request->input('flagkkbb')=='APK')
            {
                if ($arrDetail[$i]['jenis']=='D')
                {
                    $sum = $sum + $arrDetail[$i]['amount'];
                }
                else
                {
                    $sum = $sum - $arrDetail[$i]['amount'];
                }
            }
            else
            {
                if ($arrDetail[$i]['jenis']=='K')
                {
                    $sum = $sum + $arrDetail[$i]['amount'];
                }
                else
                {
                    $sum = $sum - $arrDetail[$i]['amount'];
                }
            }
        }

        if (floatval($sum) != floatval($request->input('total'))) {
            return $this->responseError('data yang diinput belum balance', 400);
        }

        DB::beginTransaction();

        try
        {
            $hasilvoucherid = $cfheader->beforeAutoNumber($request->input('flagkkbb'), $request->input('transdate'),$request->input('company_code'));

            $insertheader = $cfheader->insertData([
                'voucherid' => $hasilvoucherid,
                'transdate' => $request->input('transdate'),
                'actor' => $request->input('actor'),
                'bankid' => $request->input('bank_id'),
                'note' => $request->input('note') ?? '',
                'flagkkbb' => $request->input('flagkkbb'),
                'currid' => $request->input('currid') ?? 'IDR',
                'total' => $request->input('total'),
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
                'company_id' => $request->input('company_id')
            ]);

            if ($insertheader == false) {
                DB::rollBack();
                return $this->responseError('Gagal menyimpan data Transaksi', 500);
            }

            for ($i = 0; $i < sizeof($arrDetail); $i++)
            {

                $insertdetail = $cfdetail->insertData([
                    'voucherid' => $hasilvoucherid,
                    'rekeningid' => $arrDetail[$i]['rekeningid'],
                    'note' => $arrDetail[$i]['note'],
                    'amount' => $arrDetail[$i]['amount'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'jenis' => $arrDetail[$i]['jenis']
                ]);

                if ($insertdetail == false) {
                    DB::rollBack();
                    return $this->responseError('Gagal menyimpan data detail Transaksi', 500);
                }
            }

            $arrDetailFoto = $request->input('detailfoto');

            $deletefoto = $model_allfoto->deleteData($hasilvoucherid);

            if (!empty($arrDetailFoto) && is_array($arrDetailFoto)) {

                for ($j = 0; $j < sizeof($arrDetailFoto); $j++) {

                    $insertfoto = $model_allfoto->insertData([
                        'id' => $hasilvoucherid,
                        'foto' => $arrDetailFoto[$j]['foto'],
                        'keterangan' => 'Bukti Transaksi Nota '.$hasilvoucherid,
                        'fgtrans' => 2,
                        'upduser' => Auth::user()->currentAccessToken()['namauser']
                    ]);

                    if ($insertfoto == false) {
                        DB::rollBack();

                        return $this->responseError('insert foto gagal', 400);
                    }
                }
            }

            DB::commit();
            return $this->responseSuccess('Data Transaksi berhasil disimpan', 200, ['Voucher' => $hasilvoucherid]);


        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function getListData(GetRequest $request)
    {
        $cfheader = new CFTrKKBBHd();
        $cfdetail = new CFTrKKBBDt();

        $user = new User();

        $level = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($level->kdjabatan=='ADM')
        {
            $result = $cfheader->getListData(
                [
                    'dari' => $request->input('dari'),
                    'sampai' => $request->input('sampai'),
                    'flagkkbb' => $request->input('flagkkbb'),
                    'bankid' => $request->input('bank_id') ?? '',
                    'actorkeyword' => $request->input('actorkeyword') ?? '',
                    'voucherkeyword' => $request->input('voucherkeyword') ?? '',
                    'sortby' => $request->input('sortby') ?? 'old'
                ]
            );
        }
        else
        {
           $result = $cfheader->getListData(
                [
                    'dari' => $request->input('dari'),
                    'sampai' => $request->input('sampai'),
                    'flagkkbb' => $request->input('flagkkbb'),
                    'bankid' => $request->input('bank_id') ?? '',
                    'actorkeyword' => $request->input('actorkeyword') ?? '',
                    'voucherkeyword' => $request->input('voucherkeyword') ?? '',
                    'sortby' => $request->input('sortby') ?? 'old',
                    'company_id' => Auth::user()->currentAccessToken()['company_id']
                ]
            );
        }

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

    public function getDataById(GetRequestById $request)
    {
        $cfheader = new CFTrKKBBHd();
        $cfdetail = new CFTrKKBBDt();
        $model_foto = new AllFoto();

        $resultheader = $cfheader->getdata(
            [
                'voucher_id' => $request->input('voucher_id')
            ]
        );

        if ($resultheader) {
            $header = $resultheader;

            $detail_result = $cfdetail->getdata([
                'voucher_id' => $request->input('voucher_id') ?? ''
            ]);

            $detail = !empty($detail_result) ? $detail_result : [];

            $detail_foto_result = $model_foto->getDataById($request->input('voucher_id') ?? '');

            $detailfoto = !empty($detail_foto_result) ? $detail_foto_result : [];
        }
        else {
            $header = [];
            $detail = [];
            $detailfoto = [];
        }

        $response = [
            'header' => $header,
            'detail' => $detail,
            'detailfoto' => $detailfoto
        ];

        return $this->responseData($response);

    }

    public function updateAllData(UpdateRequest $request)
    {
        $cfheader = new CFTrKKBBHd();
        $cfdetail = new CFTrKKBBDt();
        $msrekening = new Rekening();
        $msbank = new Bank();
        $mssupplier = new Supplier();
        $model_allfoto = new AllFoto();

        if ($request->input('flagkkbb')=='JU' and $request->input('total')<>0){
            return $this->responseError('Transaksi Jurnal Umum harus memiliki total 0', 400);
        }

        if ($request->input('flagkkbb')=='ARB' or $request->input('flagkkbb')=='APB' or $request->input('flagkkbb')=='BK' or $request->input('flagkkbb')=='BM'){
            $cek = $msbank->cekData($request->input('bank_id'));
            if($cek==false){
                return $this->responseError('kode bank tidak terdaftar dalam master', 400);
            }
        }

        if ($request->input('flagkkbb')=='APB' or $request->input('flagkkbb')=='APK'){
            $cek = $mssupplier->cekData($request->input('actor'));
            if($cek==false){
                return $this->responseError('kode supplier tidak terdaftar dalam master', 400);
            }
        }

        $cek = $cfheader->cekVoucher($request->input('voucher_id') ?? '');

        if ($cek == false) {

            return $this->responseError('nomor voucher tidak ada atau tidak ditemukan', 400);
        }

        $company_id = $cek->company_id ?? '';

        $arrDetail = $request->input('detail');
        $sum = 0.00;
        for ($i = 0; $i < sizeof($arrDetail); $i++)
        {
            $cek = $msrekening->cekData($arrDetail[$i]['rekeningid']);

            if($cek==false){
                return $this->responseError('kode rekening tidak terdaftar dalam master', 400);
            }

            if ($request->input('flagkkbb')=='KK' or $request->input('flagkkbb')=='BK' or $request->input('flagkkbb')=='APB' or $request->input('flagkkbb')=='APK')
            {
                if ($arrDetail[$i]['jenis']=='D')
                {
                    $sum = $sum + $arrDetail[$i]['amount'];
                }
                else
                {
                    $sum = $sum - $arrDetail[$i]['amount'];
                }
            }
            else
            {
                if ($arrDetail[$i]['jenis']=='K')
                {
                    $sum = $sum + $arrDetail[$i]['amount'];
                }
                else
                {
                    $sum = $sum - $arrDetail[$i]['amount'];
                }
            }
        }

        if (floatval($sum) != floatval($request->input('total'))) {
            return $this->responseError('data yang diinput belum balance', 400);
        }

        DB::beginTransaction();

        try
        {
            $insertheader = $cfheader->updateAllData([
                'voucherid' => $request->input('voucher_id'),
                'transdate' => $request->input('transdate'),
                'actor' => $request->input('actor'),
                'bankid' => $request->input('bank_id'),
                'note' => $request->input('note') ?? '',
                'currid' => $request->input('currid') ?? 'IDR',
                'total' => $request->input('total'),
                'upduser' => Auth::user()->currentAccessToken()['namauser']
            ]);

            if ($insertheader == false) {
                DB::rollBack();
                return $this->responseError('Gagal mengupdate data Transaksi', 500);
            }

            $deletedetail = $cfdetail->deleteData([
                'voucherid' => $request->input('voucher_id')
            ]);

            for ($i = 0; $i < sizeof($arrDetail); $i++)
            {
                $insertdetail = $cfdetail->insertData([
                    'voucherid' => $request->input('voucher_id'),
                    'rekeningid' => $arrDetail[$i]['rekeningid'],
                    'note' => $arrDetail[$i]['note'],
                    'amount' => $arrDetail[$i]['amount'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'jenis' => $arrDetail[$i]['jenis']
                ]);

                if ($insertdetail == false) {
                    DB::rollBack();
                    return $this->responseError('Gagal mengupdate data detail Transaksi', 500);
                }

            }

            $arrDetailFoto = $request->input('detailfoto');

            $deletefoto = $model_allfoto->deleteData($request->input('voucher_id'));

            if (!empty($arrDetailFoto) && is_array($arrDetailFoto)) {

                for ($j = 0; $j < sizeof($arrDetailFoto); $j++) {

                    $insertfoto = $model_allfoto->insertData([
                        'id' => $request->input('voucher_id'),
                        'foto' => $arrDetailFoto[$j]['foto'],
                        'keterangan' => 'Bukti Transaksi Nota '.$request->input('voucher_id'),
                        'fgtrans' => 2,
                        'upduser' => Auth::user()->currentAccessToken()['namauser']
                    ]);

                    if ($insertfoto == false) {
                        DB::rollBack();

                        return $this->responseError('insert foto gagal', 400);
                    }
                }
            }

            DB::commit();
            return $this->responseSuccess('Data Transaksi berhasil diupdate', 200, ['Voucher' => $request->input('voucher_id')]);

        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function deleteData(DeleteRequest $request)
    {
        $cfheader = new CFTrKKBBHd();
        $model_allfoto = new AllFoto();

        $id = $request->input('voucher_id');

        $cek = $cfheader->cekVoucher($id);
        if (is_null($cek)) {
            return $this->responseError('Data tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try
        {
            $deleted = $cfheader->deleteData($id);

            if ($deleted == false) {
                return $this->responseError('Gagal menghapus data Transaksi', 500);
            }

            $deletefoto = $model_allfoto->deleteData($id);

            DB::commit();
            return $this->responseSuccess('Data Transaksi berhasil dihapus', 200, ['Voucher' => $id]);
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function cariNota(GetNotaRequest $request)
    {
        $cfdetail = new CFTrKKBBDt();
        $mssupplier = new Supplier();

        $cek = $mssupplier->cekData($request->input('actor'));

        if($cek==false){

            return $this->responseError('supplier tidak ada atau tidak terdaftar dalam master', 400);
        }

        $result = $cfdetail->cariNota(
            [
                'transdate' => $request->input('transdate'),
                'actor' => $request->input('actor'),
                'company_id' => $request->input('company_id') ?? Auth::user()->currentAccessToken()['company_id']
            ]
        );

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>
