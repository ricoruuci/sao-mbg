<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RptFinance;
use App\Models\Rekening;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RptFinance\GetRequestBukuBesar;
use App\Http\Requests\RptFinance\GetRequestLabaRugi;
use App\Http\Requests\RptFinance\GetRequestNeraca;
use App\Http\Requests\RptFinance\GetRequestCosting;

class RptFinanceController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getRptBukuBesar(GetRequestBukuBesar $request)
    {
        $model = new RptFinance();
        $rekening = new Rekening();

        $rekeningId = trim((string) $request->input('rekening_id', ''));

        if ($rekeningId !== '') {
            $cek = $rekening->cekData($rekeningId);
            if ($cek == false) {
                return $this->responseError('Rekening not found', 404);
            }

            $rekeningFields = is_object($cek) ? get_object_vars($cek) : [];
            foreach ($rekeningFields as $key => $value) {
                if (strtolower((string) $key) === 'rekeningid') {
                    $rekeningId = trim((string) $value);
                    break;
                }
            }
        }

        $result = $model->getRptBukuBesar([
            'dari' => $request->input('dari'),
            'sampai' => $request->input('sampai'),
            'rekening_id' => $rekeningId,
            'fg_transaksi' => $request->input('fg_transaksi') ?? 'TR'
        ]);

        return $this->responseData($result);
    }

    public function getRptLabaRugi(GetRequestLabaRugi $request)
    {
        $model = new RptFinance();

        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='USR')
        {
            $result = $model->getRptLabaRugi([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'company_id' => Auth::user()->currentAccessToken()['company_id'],
                'fg_transaksi' => $request->input('fg_transaksi') ?? 'TR'
            ]);
        }
        else
        {
            $result = $model->getRptLabaRugi([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'company_id' => $request->input('company_id', Auth::user()->currentAccessToken()['company_id']),
                'fg_transaksi' => $request->input('fg_transaksi') ?? 'TR'
            ]);
        }

        return $this->responseData($result);
    }

    public function getRptNeraca(GetRequestNeraca $request)
    {
        $model = new RptFinance();

        $result = $model->getRptNeraca([
            'periode' => $request->input('periode'),
        ]);

        return $this->responseData($result);
    }

    public function getRptCosting(GetRequestCosting $request)
    {
        $model = new RptFinance();

        $data = $model->getRptCosting([
            'dari' => $request->input('dari'),
            'sampai' => $request->input('sampai'),
        ]);

        $collection = collect($data);

        // ambil data per tipe
        $A1 = $collection->where('tipe', 'A1')->values(); // bahan baku anggaran
        $A2 = $collection->where('tipe', 'A2')->values(); // operasional anggaran
        $B1 = $collection->where('tipe', 'B1')->values(); // bahan baku realisasi
        $B2 = $collection->where('tipe', 'B2')->values(); // operasional realisasi

        // total
        $totalA1 = (float)$A1->sum(fn($i) => $i->amount);
        $totalA2 = (float)$A2->sum(fn($i) => $i->amount);
        $totalB1 = (float)$B1->sum(fn($i) => $i->amount);
        $totalB2 = (float)$B2->sum(fn($i) => $i->amount);

        // margin
        $marginBahanBaku = $totalA1 - $totalB1;
        $marginOperasional = $totalA2 - $totalB2;

        $result = [
            [
                'judul' => 'ANGGARAN',
                'total' => $totalA1 + $totalA2,
                'detail' => [
                    [
                        'keterangan' => 'BAHAN BAKU',
                        'total' => $totalA1,
                        'detail_data' => $A1->map(fn($i) => [
                            'id' => $i->id,
                            'amount' => (float)$i->amount
                        ])->values()
                    ],
                    [
                        'keterangan' => 'OPERASIONAL',
                        'total' => $totalA2,
                        'detail_data' => $A2->map(fn($i) => [
                            'id' => $i->id,
                            'amount' => (float)$i->amount
                        ])->values()
                    ]
                ]
            ],
            [
                'judul' => 'REALISASI',
                'total' => $totalB1 + $totalB2,
                'detail' => [
                    [
                        'keterangan' => 'BAHAN BAKU',
                        'total' => $totalB1,
                        'detail_data' => $B1->map(fn($i) => [
                            'id' => $i->id,
                            'amount' => (float)$i->amount
                        ])->values()
                    ],
                    [
                        'keterangan' => 'OPERASIONAL',
                        'total' => $totalB2,
                        'detail_data' => $B2->map(fn($i) => [
                            'id' => $i->id,
                            'amount' => (float)$i->amount
                        ])->values()
                    ]
                ]
            ],
            [
                'judul' => 'MARGIN',
                'total' => $marginBahanBaku + $marginOperasional,
                'detail' => [
                    [
                        'keterangan' => 'BAHAN BAKU',
                        'total' => $marginBahanBaku
                    ],
                    [
                        'keterangan' => 'OPERASIONAL',
                        'total' => $marginOperasional
                    ]
                ]
            ]
        ];

        return [
            'data' => $result
        ];
    }
}

?>
