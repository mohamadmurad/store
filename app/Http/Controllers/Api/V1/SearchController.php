<?php

namespace App\Http\Controllers\Api\V1;

use App\Categories;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\roles\RolesResource;
use App\Http\Resources\User\UserResource;
use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class SearchController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:employee_search')->only('employeeSearchByEmail');
        $this->middleware('permission:category_search')->only('categorySearch');
    }



    public function employeeSearchByEmail(Request $request){

        $rules = [
            'email' => 'required',
        ];

        $this->validate($request,$rules);

        $email = trim($request->email);

        $employees = User::Role(['super_employee','employee'])->doesnthave('branch')->where('email','like','%'. $email .'%')->get();

        return UserResource::collection($employees);
    }

    public function categorySearch(Request $request){

        $rules = [
            'name' => 'required',
        ];

        $this->validate($request,$rules);

        $name = trim($request->name);

        $categories = Categories::where('name','like','%'. $name .'%')->with('children')->get();

        return CategoryResource::collection($categories);
    }
}
