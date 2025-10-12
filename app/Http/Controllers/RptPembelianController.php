<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RptPembelian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RptPembelian\GetLapPembelianRequest;
use App\Http\Requests\RptPembelian\GetLapHutangRequest;

class RptPembelianController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getLapPembelian(GetLapPembelianRequest $request)
    {
        $model = new RptPembelian();
        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='ADM')
        {
            $result = $model->getLapPembelian([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'search_keyword' => $request->input('search_keyword', ''),
                'supplier_keyword' => $request->input('supplier_keyword', '')
            ]);
        }
        else
        {
            $result = $model->getLapPembelian([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'search_keyword' => $request->input('search_keyword', ''),
                'supplier_keyword' => $request->input('supplier_keyword', ''),
                'company_id' => Auth::user()->currentAccessToken()['company_id']
            ]);
        }

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function getLapHutang(GetLapHutangRequest $request)
    {
        $model = new RptPembelian();
        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='ADM')
        {
            $result = $model->getLapHutang([
                'transdate' => $request->input('transdate'),
                'search_keyword' => $request->input('search_keyword', ''),
                'supplier_keyword' => $request->input('supplier_keyword', '')
            ]);
        }
        else
        {
            $result = $model->getLapHutang([
                'transdate' => $request->input('transdate'),
                'search_keyword' => $request->input('search_keyword', ''),
                'supplier_keyword' => $request->input('supplier_keyword', ''),
                'company_id' => Auth::user()->currentAccessToken()['company_id']
            ]);
        }

        

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

}

?>
