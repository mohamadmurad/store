<?php

namespace App\Http\Controllers\Api\V1;

use App\Categories;
use App\Http\Controllers\Controller;
use App\Http\Resources\roles\RolesResource;
use App\Http\Resources\User\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class toolsController extends Controller
{


    public function __construct()
    {
        $this->middleware('role:super_admin')->only(['getAllRoles']);


    }

    public function getAllRoles(){

        if (request()->expectsJson() && request()->acceptsJson()){
            $roles = Role::all();
            return RolesResource::collection($roles);

        }
        return null;

    }



}
