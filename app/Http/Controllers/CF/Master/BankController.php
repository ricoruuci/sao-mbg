<?php

namespace App\Http\Controllers\CF\Master; //ini cek foldernya

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CF\Master\CFMsBank; //cek nama model nya
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getListData(Request $request)
    {
        $bank = new CFMsBank();

        $result = $bank->getListData([
            'bankidkeyword' => $request->input('bankidkeyword'),
            'banknamekeyword' => $request->input('banknamekeyword')
        ]);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }
}
