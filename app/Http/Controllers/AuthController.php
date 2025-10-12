<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;

class AuthController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function login(Request $request, User $user)
    {

        $isLoginSuccess = $user->isLoginValid($request->userid, $request->password);

        if ($isLoginSuccess == false)
        {
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Invalid username or password'
            // ]);

            return $this->responseError('Invalid username or password', 400);
            
        }
        else
        {
            $userData = User::where('userid', $request->userid)->first();

            DB::beginTransaction();

            try
            {

                $token = $userData->createToken('API Token');

                // die(var_dump($token->accessToken->id));
                $user->updateData($token->accessToken->id);

                DB::commit();

                $cabang = $user->getCabang($request->userid);

                return response()->json([
                    'user_data' => $userData,
                    'cabang' => $cabang,
                    'token' => $token->plainTextToken
                ]);

            }
            catch (\Exception $e)
            {
                DB::rollBack();

                return $this->responseError($e->getMessage(), 400);
            }



        }
    }

    public function logout(Request $request, User $user)
    {
        $currentAccessToken = Auth::user()->currentAccessToken();

        $tokenableId = $currentAccessToken['tokenable_id'];

        DB::beginTransaction();

        try
        {

            $user->deleteData(['tokenable_id' => $tokenableId]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Logout success'
            ]);

        }
        catch (\Exception $e)
        {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }


    }
}
