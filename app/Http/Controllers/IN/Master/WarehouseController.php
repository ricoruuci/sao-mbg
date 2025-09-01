<?php

namespace App\Http\Controllers\IN\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IN\Master\INMsWarehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getListData(Request $request)
    {
        $warehouse = new INMsWarehouse();

        $result = $warehouse->getListData([
            'warehouseidkeyword' => $request->input('warehouseidkeyword'),
            'warehousenamekeyword' => $request->input('warehousenamekeyword')
        ]);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function getData(Request $request, $warehouseid)
    {
        $warehouse = new INMsWarehouse();

        $result = $warehouse->getdata(
            [
                'warehouseid' => $warehouseid
            ]
        );

        return $this->responseData($result);
    }
}
