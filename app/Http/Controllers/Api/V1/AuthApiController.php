<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;

class AuthApiController extends Controller
{

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


            /// verify email and phone code
            ///
            ///
            ///

            return $this->successResponse([
                'message' => 'User Saved',
            ],201);;

        }
        return null;

    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $delete = $user->token()->delete();

        if($delete){
            return $this->showMessage('Logout success.',200);
        }else{
            return $this->errorResponse('Error in logout.',401);
        }
    }

    public function login() {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {


            return $this->getTokenAndRefreshToken(request('email'), request('password'));
        }
        else {

            return response()->json(['error'=>'Unauthorized. email or password error!'], 401);
        }
    }

    public function getTokenAndRefreshToken($email, $password) {

        $client = Client::where('password_client', 1)->first();

        if (!$client){
            return $this->errorResponse('Sorry login is not work now please come back later.',403);
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
