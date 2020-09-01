<?php

namespace App\Http\Controllers\Api\V1;

use App\Branches;
use App\Cards;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Resources\roles\RolesResource;
use App\Http\Resources\User\UserResource;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use Spatie\Permission\Models\Role;

class AuthApiController extends Controller
{

    public function __construct()
    {

    }

    use ApiResponser;
    private $getTokenURI = 'http://127.0.0.1:8000/api/v1/oauth/token';


    public function registerNewUserAccount(RegisterRequest $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()){

            $newUser = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'username' => $request->get('username'),
                'location' => $request->get('location'),
                'password' => bcrypt($request->get('password')),
            ]);

            $newUser->assignRole('customer');


            /// verify email and phone code
            ///
            ///
            ///
            ///
            ///

            return new UserResource($newUser);
//            return $this->successResponse([
//                'message' => 'User Saved',
//                'code' => 201,
//            ],201);;

        }
        return null;

    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $delete = $user->token()->delete();

        if($delete){
            return $this->showMessage(trans('success.logout'),200);
        }else{
            return $this->errorResponse(trans('error.logout'),401);
        }
    }

    public function login(LoginRequest $request) {
        if (Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])) {


            return $this->getTokenAndRefreshToken($request->get('email'), $request->get('password'));
        }
        else {

            return response()->json(['error'=>trans('error.attempt')], 401);
        }
    }

    public function getTokenAndRefreshToken($email, $password) {

        $client = Client::where('password_client', 1)->first();

        if (!$client){
            return $this->errorResponse(trans('error.passport_client'),403);
        }

        $data = [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $email,
            'password' => $password,
            'scope' => '*',
        ];


        $r = Request::create($this->getTokenURI,'post',$data);
        $content = json_decode(app()->handle($r)->getContent());

        return $this->successResponse($content,200);
        //return response()->json($content, 200);
    }


}
