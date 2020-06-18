<?php

namespace App\Http\Middleware;

use App\Branches;
use App\Products;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Support\Facades\Auth;

class checkIfUserHasProduct
{

    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {




        $user = Auth::user();

        $product_branch_id = Products::findOrFail($request->employee_product)->branch_id;
        $employee_branch_id = Branches::where('user_id',$user->id)->first()->id;


        if ((int)$employee_branch_id === (int)$product_branch_id){

            return $next($request);
        }else{

            return $this->errorResponse('You can\'t access this product',404);
        }






    }
}
