<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RptInventory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RptInventory\GetLapStockRequest;
use App\Http\Requests\RptInventory\GetLapKartuStockRequest;

class RptInventoryController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getLapStock(GetLapStockRequest $request)
    {
        $model = new RptInventory();
        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='ADM')
        {
            $result = $model->getLapStock([
                'transdate' => $request->input('transdate'),
                'search_keyword' => $request->input('search_keyword', ''),
                'show_zero' => $request->input('show_zero', 'T')
            ]);
        }
        else
        {
            $result = $model->getLapStock([
                'transdate' => $request->input('transdate'),
                'search_keyword' => $request->input('search_keyword', ''),
                'show_zero' => $request->input('show_zero', 'T'),
                'company_id' => Auth::user()->currentAccessToken()['namauser']
            ]);
        }

        return $this->responseData($result);
    }

    public function getLapKartuStock(GetLapKartuStockRequest $request)
    {
        $model = new RptInventory();
        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='ADM')
        {
            $result = $model->getLapKartuStock([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'search_keyword' => $request->input('search_keyword', '')
            ]);
        }
        else
        {
            $result = $model->getLapKartuStock([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'search_keyword' => $request->input('search_keyword', ''),
                'company_id' => Auth::user()->currentAccessToken()['namauser']
            ]);
        }

        

        return $this->responseData($result);
    }

}

?>
