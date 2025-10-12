<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroupRekening;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Http\Requests\GroupRekening\GetRequest;
use Illuminate\Support\Facades\Auth;

class GroupRekeningController extends Controller
{
    use ArrayPaginator, HttpResponse;
    
    public function getListData(GetRequest $request)
    {
        $group_model = new GroupRekening();

        $params = [
            'search_keyword' => $request->search_keyword ?? '',
        ];

        $result = $group_model->getAllData($params);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>

