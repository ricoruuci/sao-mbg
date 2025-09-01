<?php

namespace App\Http\Controllers\CF\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CF\Report\RptKKBB;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class RptKKBBController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getLaporanKas(Request $request)
    {
        $item = new RptKKBB();

        $result = $item->getLaporanKas(
            [
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai')
            ]
        );

        return $this->responseData($result);
    }

    public function getLaporanBank(Request $request)
    {
        $item = new RptKKBB();

        $result = $item->getLaporanBank(
            [
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'bankid' => $request->input('bankid')
            ]
        );

        return $this->responseData($result);
    }
}
