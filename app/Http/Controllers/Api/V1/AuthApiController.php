<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;

class AuthApiController extends Controller
{

    use ApiResponser;
    private $getTokenURI = 'http://127.0.0.1:8000/api/v1/oauth/token';


    public function register(RegisterAuthRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        if ($this->loginAfterSignUp) {
            return $this->login($request);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], Response::HTTP_OK);
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
            /////////
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function regist2er(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $password = $request->password;
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $oClient = OClient::where('password_client', 1)->first();
        return $this->getTokenAndRefreshToken($oClient, $user->email, $password);
    }

    public function getTokenAndRefreshToken($email, $password) {

        $client = Client::where('password_client', 1)->first();

        if (!$client){
            return $this->errorResponse('login was not work please come back later ! .',401);
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
