<?php

namespace App\Http\Controllers\Api\V1;

use App\Cards;
use App\Categories;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\roles\RolesResource;
use App\Http\Resources\User\UserResource;
use App\Orders;
use App\Traits\ApiResponser;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class toolsController extends Controller
{

    use ApiResponser;


    public function __construct()
    {
        $this->middleware('permission:show_roles')->only(['getAllRoles']);
        $this->middleware('role:Super Admin')->only(['dashHome']);
        $this->middleware('role:employee|super_employee')->only(['getMyBranchCategory']);


    }

    public function getAllRoles(){

        if (request()->expectsJson() && request()->acceptsJson()){
            $roles = Role::all();
            return RolesResource::collection($roles);

        }
        return null;

    }

    public function dashHome(Request $request){

        $admin = Auth::user();
        $allCustomer = User::Role('customer')->count();
        $allCustomerInDay = User::Role('customer')->whereDate('created_at','=',Carbon::today())->count();
        $adminBalance = $admin->card->balance;

        $allAdmins = User::Role('Super Admin')->get()->pluck('id');
        $allBalance = Cards::whereIn('id',$allAdmins)->sum('balance');

        $orderChart = Orders::select( DB::raw('count(*) as total, MONTHNAME(date) month'))
            ->whereYear('date',Carbon::today()->year)
            ->groupBy('month')
            ->get();



        return response()->json([
            'data'=>[
                'allCustomer' =>$allCustomer,
                'allCustomerInDay' => $allCustomerInDay,
                'adminBalance' => $adminBalance,
                'allBalance' =>   $allBalance,
                'orderChart'=>$orderChart,
            ]

        ],200);

        return $allCustomer;
    }


    public function getMyBranchCategory(Request $request){

        $employee = Auth::user();

        $category = $employee->branch->company->category->children;
        //dd($category);
        return $this->showCollection(CategoryResource::collection($category));


    }


}
