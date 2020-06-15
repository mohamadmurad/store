<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUser;
use App\Http\Requests\User\UpdateAuthUserInfo;
use App\Http\Requests\User\UpdateUser;
use App\Http\Resources\User\AccountInfoResource;
use App\Http\Resources\User\UserResource;
use App\Traits\ApiResponser;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{


    use ApiResponser;

    public function __construct()
    {

        $this->middleware(['permission:read_user'])->only(['index','show']);
        $this->middleware(['permission:add_user','addUser'])->only('store');
        $this->middleware(['permission:edit_user','checkUserPassword'])->only('update');
        $this->middleware(['permission:delete_user'])->only('destroy');

    }

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
     * @return UserResource|JsonResponse|Response
     */
    public function store(StoreUser $request)
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

            $newUser->syncRoles($request->roles);


            return $this->successResponse([
                'message' => 'User Saved',
            ],201);;

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
     * Display the auth user info.
     *
     * @return AccountInfoResource
     */
    public function getMyInfo()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return new AccountInfoResource(Auth::user());
            //return new UserResource($user);
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
                    'message'=> 'you need to specify a different value to update',
                    'code'=> 422],
                    422);
            }

            $user->save();
            return $this->successResponse([
                'message' => 'User Info Changed',
            ],200);


        }
        return null;
    }


    /**
     * Update the auth user info.
     *
     * @param UpdateAuthUserInfo $request
     * @return AccountInfoResource|JsonResponse
     */
    public function updateMyInfo(UpdateAuthUserInfo $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()){

            $user = Auth::user();

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
                    'message'=> 'you need to specify a different value to update',
                    'code'=> 422],
                    422);
            }

            $user->save();
            return new AccountInfoResource($user);;


        }
        return null;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(User $user)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $user->delete();
            return $this->successResponse([
                'message' => 'User deleted',
            ],200);
        }
        return null;

    }
}