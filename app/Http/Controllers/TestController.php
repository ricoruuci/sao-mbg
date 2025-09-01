<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class TestController extends Controller
{
    use HttpResponse;

    public function gettest()
    {
          return 'berhasil'; 
    }

    public function posttest(Request $request)
   {
          return 'berhasil';  
   }
   
}
