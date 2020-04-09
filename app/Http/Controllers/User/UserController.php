<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showAll($users);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $rules = [
            'name'=>'required|string|min:5|max:100',
            'email'=>'required|email|max:255|unique:users,email',
            'phone'=>'required|unique:users,phone',
            'username'=>'required|unique:users,username',
            'location'=>'required|string',
            'password'=>'required|min:8|confirmed',
        ];

        $this->validate($request,$rules);
        $request->password = bcrypt(request()->password);
        $newUser = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'username' => $request->get('username'),
            'location' => $request->get('location'),
            'password' => bcrypt($request->get('password')),
            ]);
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($newUser);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {

        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($user);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, User $user)
    {
        $rules = [];
        $rules += [
            'password'=>'required|min:8',
        ];
        $this->validate($request,$rules);

        if(!Hash::check($request->password, $user->password)){
            return $this->errorResponse([
                'error'=> 'your password is not correct',
                'code'=> 422],
                422);
        }


        if($request->has(['name'])){
            $rules += [
                'name'=>'required|string|min:5|max:100',
            ];

            $user->fill($request->only([
                'name',
            ]));
        }
        if($request->has(['email'])){
            $rules += [
                'email' =>['required','email','max:255',
                    Rule::unique($user->getTable())->ignore(request()->segment(3))
                ],
            ];

            $user->fill($request->only([
                'email',
            ]));
        }
        if($request->has(['phone'])){
            $rules += [
                'phone'=>['required',
                    Rule::unique($user->getTable())->ignore(request()->segment(3))
                    ],

            ];

            $user->fill($request->only([
                'phone',
            ]));
        }

        if($request->has(['username'])){
            $rules += [
                'username'=>['required',
                    Rule::unique($user->getTable())->ignore(request()->segment(3))
                ],
            ];

            $user->fill($request->only([
                'username',
            ]));
        }

        if($request->has(['location'])){
            $rules += [
                'location'=>'required|string',
            ];

            $user->fill($request->only([
                'location',
            ]));
        }


        if($request->has(['newPassword'])){
            $rules += [
                'newPassword'=>'required|min:8|confirmed|different:password',
            ];

            $user->fill([
                'password' => bcrypt($request->get('newPassword')),
            ]);
        }

        $this->validate($request,$rules);





        if($user->isClean()){
            return $this->errorResponse([
                'error'=> 'you need to specify a different value to update',
                'code'=> 422],
                422);
        }

        $user->save();
        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->showOne($user);
    }
}
