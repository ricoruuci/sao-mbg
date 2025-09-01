<?php

namespace App\Http\Controllers\AP\Activity;

use App\Http\Controllers\Controller;
use App\Models\AP\Activity\PembelianDt;
use App\Models\AP\Activity\PembelianDtSN;
use App\Models\AP\Activity\PembelianHd;
use App\Models\AP\Master\APMsSupplier;
use App\Models\IN\Master\INMsItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class PembelianController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {

        $pembelian = new PembelianHd();
        $modelDetail = new PembelianDt();
        $modelSn = new PembelianDtSN();

        $mssupplier = new APMsSupplier();
        $msitem = new INMsItem();

        $validator = Validator::make($request->all(), $pembelian::$rulesInsert, $pembelian::$messagesInsert);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        }

        $validatorDetail = Validator::make($request->all(), $modelDetail::$rulesInsert, $modelDetail::$messagesInsert);

        if ($validatorDetail->fails()) {
            return $this->responseError($validatorDetail->messages(), 400);
        }

        $validatorSn = Validator::make($request->all(), $modelSn::$rulesInsert, $modelSn::$messagesInsert);

        if ($validatorSn->fails()) {
            return $this->responseError($validatorSn->messages(), 400);
        }

        $cek = $mssupplier->cekSupplier($request->input('suppid'));

        if ($cek == false) {

            return $this->responseError('kode supplier tidak terdaftar dalam master', 400);
        }

        $suppname = $cek->SuppName;

        DB::beginTransaction();

        try {
            $hasilpurchaseid = $pembelian->beforeAutoNumber($request->input('transdate'));

            //dd(var_dump($hasilpurchaseid));

            $insertheader = $pembelian->insertData([
                'purchaseid' => $hasilpurchaseid,
                'transdate' => $request->input('transdate'),
                'fpsid' => $request->input('fpsid') ?? '',
                'suppid' => $request->input('suppid'),
                'nilaitax' => $request->input('nilaitax') ?? '11',
                'fgtax' => $request->input('fgtax') ?? 'T',
                'note' => $request->input('note') ?? '',
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
            ]);


            //dd($insertheader);

            if ($insertheader == false) {


                DB::rollBack();


                return $this->responseError('insert header gagal', 400);
            }

            $arrDetail = $request->input('detail');

            for ($i = 0; $i < sizeof($arrDetail); $i++) {

                $cek = $msitem->cekBarang($arrDetail[$i]['itemid']);

                if ($cek == false) {

                    DB::rollBack();

                    return $this->responseError('kode mobil tidak terdaftar dalam master', 400);
                }

                $insertdetail = $modelDetail->insertData([
                    'purchaseid' => $hasilpurchaseid,
                    'suppid' => $request->input('suppid'),
                    'itemid' => $arrDetail[$i]['itemid'],
                    'qty' => $arrDetail[$i]['qty'] ?? 1,
                    'price' => $arrDetail[$i]['price'] ?? 0,
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);


                if ($insertdetail == false) {
                    DB::rollBack();

                    return $this->responseError('insert detail gagal', 400);
                }

                $insertallitem = $pembelian->insertAllItem([
                    'purchaseid' => $hasilpurchaseid,
                    'transdate' => $request->input('transdate'),
                    'itemid' => $arrDetail[$i]['itemid'],
                    'price' => $arrDetail[$i]['price'],
                    'qty' => $arrDetail[$i]['qty'],
                    'suppname' => $suppname

                ]);

                //dd(var_dump($insertheader));

                if ($insertallitem == false) {

                    DB::rollBack();

                    return $this->responseError('insert allitem gagal', 400);
                }

                if (isset($arrDetail[$i]['detailsn'])) {

                    $arrDetailSn = $arrDetail[$i]['detailsn'];

                    if (sizeof($arrDetailSn) <> $arrDetail[$i]['qty']) {
                        DB::rollBack();

                        return $this->responseError('jumlah nopol tidak sama dengan jumlah barang (kode: ' . $arrDetail[$i]['itemid'] . ')', 400);
                    }


                    for ($u = 0; $u < sizeof($arrDetailSn); $u++) {

                        /*$cek = $msitem->cekBarang($arrDetail[$i]['itemid']);

                        if ($cek == false) {

                            DB::rollBack();

                            return $this->responseError('kode barang tidak terdaftar dalam master', 400);
                        }*/


                        $insertdetail = $modelSn->insertData([
                            'purchaseid' => $hasilpurchaseid,
                            'suppid' =>  $request->input('suppid'),
                            'itemid' => $arrDetail[$i]['itemid'],
                            'snid' => $arrDetailSn[$u]['snid'],
                            'price' => $arrDetail[$i]['price'],
                            'upduser' => Auth::user()->currentAccessToken()['namauser']
                        ]);

                        if ($insertdetail == false) {

                            DB::rollBack();

                            return $this->responseError('insert nopol gagal', 400);
                        }
                    }
                }
            }

            $hitung = $pembelian->hitungTotal([
                'purchaseid' => $hasilpurchaseid,
            ]);

            $pembelian->updateTotal([
                'grandtotal' => $hitung->grandtotal,
                'subtotal' => $hitung->subtotal,
                'ppn' => $hitung->ppn,
                'purchaseid' => $hasilpurchaseid
            ]);



            //dd(var_dump($hitung));
            DB::commit();

            return $this->responseSuccess('insert berhasil', 200, ['nota' => $hasilpurchaseid]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }

    /*public function generateSN(Request $request)
    {
        // Ambil input dari request
        $grnid = $request->input('grnid');
        $itemid = $request->input('itemid');
        $qty = $request->input('qty');

        // Buat instance dari model KonsinyasiDtSN
        $modelSn = new KonsinyasiDtSN();

        // Panggil method autoGenerateSN di model untuk generate serial numbers
        $generatedSNs = $modelSn->autoGenerateSN($grnid, $itemid, $qty);

        $result = [];
        foreach ($generatedSNs as $snid) {
            $autosn[] =  [
                'snid' => $snid
            ];
        }
        $result = ['data' => $autosn];

        // Return hasil SN dalam format yang diinginkan
        return response()->json($result);
    }*/

    public function updateAllData(Request $request)
    {
        $pembelian = new PembelianHd();
        $modelDetail = new PembelianDt();
        $modelSn = new PembelianDtSN();

        $mssupplier = new APMsSupplier();
        $msitem = new INMsItem();

        $validator = Validator::make($request->all(), $pembelian::$rulesUpdateAll, $pembelian::$messagesUpdate);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        }

        $validatorDetail = Validator::make($request->all(), $modelDetail::$rulesInsert, $modelDetail::$messagesInsert);

        if ($validatorDetail->fails()) {
            return $this->responseError($validatorDetail->messages(), 400);
        }

        $v = Validator::make($request->all(), $modelSn::$rulesInsert, $modelSn::$messagesInsert);

        if ($v->fails()) {
            return $this->responseError($v->messages(), 400);
        }

        $cek = $pembelian->cekInvoice($request->input('purchaseid'));

        if ($cek == false) {

            return $this->responseError('nota pembelian tidak terdaftar', 400);
        }

        $cek = $mssupplier->cekSupplier($request->input('suppid'));

        if ($cek == false) {

            return $this->responseError('kode supplier tidak terdaftar dalam master', 400);
        }

        $suppname = $cek->SuppName;

        DB::beginTransaction();

        try {
            $insertheader = $pembelian->updateAllData([
                'purchaseid' => $request->input('purchaseid'),
                'transdate' => $request->input('transdate'),
                'suppid' => $request->input('suppid'),
                'fpsid' => $request->input('fpsid') ?? '',
                'fgtax' => $request->input('fgtax') ?? '',
                'nilaitax' => $request->input('nilaitax') ?? '',
                'note' => $request->input('note') ?? '',
                'upduser' => Auth::user()->currentAccessToken()['namauser']
            ]);

            if ($insertheader == false) {

                DB::rollBack();

                return $this->responseError('update header gagal', 400);
            }

            $deletedetail = $modelDetail->deleteData([
                'purchaseid' => $request->input('purchaseid')
            ]);

            $deleteallitem = $pembelian->deleteAllItem([
                'purchaseid' => $request->input('purchaseid')
            ]);

            // if ($deletedetail == false) {

            //     DB::rollBack();

            //     return $this->responseError('something went wrong', 400);
            // }

            $arrDetail = $request->input('detail');

            for ($i = 0; $i < sizeof($arrDetail); $i++) {
                $cek = $msitem->cekBarang($arrDetail[$i]['itemid']);

                if ($cek == false) {

                    DB::rollBack();

                    return $this->responseError('kode mobil tidak terdaftar dalam master', 400);
                }


                $insertdetail = $modelDetail->insertData([
                    'purchaseid' => $request->input('purchaseid'),
                    'suppid' => $request->input('suppid'),
                    'itemid' => $arrDetail[$i]['itemid'],
                    'qty' => $arrDetail[$i]['qty'] ?? 1,
                    'price' => $arrDetail[$i]['price'] ?? 0,
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);

                if ($insertdetail == false) {

                    DB::rollBack();

                    return $this->responseError('update detail gagal', 400);
                }

                $insertallitem = $pembelian->insertAllItem([
                    'purchaseid' => $request->input('purchaseid'),
                    'transdate' => $request->input('transdate'),
                    'itemid' => $arrDetail[$i]['itemid'],
                    'price' => $arrDetail[$i]['price'],
                    'qty' => $arrDetail[$i]['qty'],
                    'suppname' => $suppname

                ]);


                if (isset($arrDetail[$i]['detailsn'])) {

                    $arrDetailSn = $arrDetail[$i]['detailsn'];

                    if (sizeof($arrDetailSn) <> $arrDetail[$i]['qty']) {
                        DB::rollBack();

                        return $this->responseError('jumlah nopol tidak sama dengan jumlah mobil (kode: ' . $arrDetail[$i]['itemid'] . ')', 400);
                    }

                    for ($u = 0; $u < sizeof($arrDetailSn); $u++) {

                        $cek = $msitem->cekBarang($arrDetail[$i]['itemid']);

                        if ($cek == false) {

                            DB::rollBack();

                            return $this->responseError('kode mobil tidak terdaftar dalam master', 400);
                        }

                        $insertdetail = $modelSn->insertData([
                            'purchaseid' => $request->input('purchaseid'),
                            'suppid' =>  $request->input('suppid'),
                            'itemid' => $arrDetail[$i]['itemid'],
                            'snid' => $arrDetailSn[$u]['snid'],
                            'price' => $arrDetail[$i]['price'],
                            'upduser' => Auth::user()->currentAccessToken()['namauser']
                        ]);

                        if ($insertdetail == false) {
                            DB::rollBack();

                            return $this->responseError('update nopol gagal', 400);
                        }
                    }
                }
            }

            $hitung = $pembelian->hitungTotal([
                'purchaseid' => $request->input('purchaseid'),
            ]);

            $pembelian->updateTotal([
                'grandtotal' => $hitung->grandtotal,
                'subtotal' => $hitung->subtotal,
                'ppn' => $hitung->ppn,
                'purchaseid' => $request->input('purchaseid')
            ]);

            DB::commit();

            return $this->responseSuccess('update berhasil', 200, ['nota' => $request->input('purchaseid')]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function getListData(Request $request)
    {
        $pembelian = new PembelianHd();
        $pembeliandt = new PembelianDt();
        //$konsinyasidtsn = new KonsinyasiDtSN();

        if ($request->input('purchaseid')) {

            $resultheader = $pembelian->getdata(
                [
                    'purchaseid' => $request->input('purchaseid')
                ]
            );

            $resultdetail = $pembeliandt->getdata(
                [
                    'purchaseid' => $request->input('purchaseid')
                ]
            );

            $result = [
                'header' => $resultheader,
                'detail' => $resultdetail
            ];

            return $this->responseData($result);
        } else {
            $result = $pembelian->getListData(
                [
                    'dari' => $request->input('dari'),
                    'sampai' => $request->input('sampai'),
                    'suppidkeyword' => $request->input('suppidkeyword') ?? '',
                    'suppnamekeyword' => $request->input('suppnamekeyword') ?? '',
                    'purchaseidkeyword' => $request->input('purchaseidkeyword') ?? '',
                    'sortby' => $request->input('sortby') ?? 'dateold'
                ]
            );

            $resultPaginated = $this->arrayPaginator($request, $result);

            return $this->responsePagination($resultPaginated);
        }
    }

    public function deleteData(Request $request)
    {
        $konsinyasiinvid = new PembelianHd();

        $cek = $konsinyasiinvid->cekInvoice($request->input('purchaseid'));

        if ($cek == false) {

            return $this->responseError('nota pembelian tidak terdaftar', 400);
        }

        DB::beginTransaction();

        try {
            $deleted = $konsinyasiinvid->deleteData([
                'purchaseid' => $request->input('purchaseid')
            ]);

            $deletallitem = $konsinyasiinvid->deleteAllItem([
                'purchaseid' => $request->input('purchaseid')
            ]);

            if ($deleted) {
                DB::commit();

                return $this->responseSuccess('hapus berhasil', 200, ['nota' => $request->input('purchaseid')]);
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
