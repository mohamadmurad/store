<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUser;
use App\Http\Requests\User\UpdateUser;
use App\Http\Resources\User\UserResource;
use App\Traits\ApiResponser;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|Response|LengthAwarePaginator
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $users = User::all();
            return $this->showCollection(UserResource::collection($users));
        }

        return null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUser $request
     * @return UserResource|Response
     */
    public function store(StoreUser $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()){

            $request->password = bcrypt($request->password);
            $newUser = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'username' => $request->get('username'),
                'location' => $request->get('location'),
                'password' => bcrypt($request->get('password')),
            ]);
            if (request()->expectsJson() && request()->acceptsJson()){
                return new UserResource($newUser);
            }
        }
        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return UserResource|Response
     */
    public function show(User $user)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return new UserResource($user);
        }
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUser $request
     * @param User $user
     * @return UserResource|JsonResponse|Response
     */
    public function update(UpdateUser $request, User $user)
    {
        if (request()->expectsJson() && request()->acceptsJson()){

            if(!Hash::check($request->password, $user->password)){
                return $this->errorResponse([
                    'error'=> 'your password is not correct',
                    'code'=> 422],
                    422);
            }

            $user->fill($request->only([
                'name',
                'email',
                'phone',
                'username',
                'location',
            ]));

            if($request->has(['newPassword'])){

                $user->fill([
                    'password' => bcrypt($request->get('newPassword')),
                ]);
            }

            if($user->isClean()){
                return $this->errorResponse([
                    'error'=> 'you need to specify a different value to update',
                    'code'=> 422],
                    422);
            }

            $user->save();
            return new UserResource($user);


        }
        return null;






    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return UserResource|Response
     * @throws Exception
     */
    public function destroy(User $user)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $user->delete();
            return new UserResource($user);
        }
        return null;

    }
}
