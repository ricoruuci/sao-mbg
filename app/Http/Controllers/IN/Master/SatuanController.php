<?php

namespace App\Http\Controllers\IN\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IN\Master\INMsUOM;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class SatuanController extends Controller
{
    use ArrayPaginator, HttpResponse;
    
    public function getListData(Request $request)
    {
        $satuan = new INMsUOM();

        $result = $satuan->getListData([
            'uomidkeyword' => $request->input('uomidkeyword')
        ]);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>