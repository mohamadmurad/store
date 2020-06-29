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

        $branch_id = $user->branch()->first()->id;


       $product_branch_id = $request->employee_product->branch_id;


        if ((int)$product_branch_id === (int)$branch_id){

            return $next($request);
        }else{

            return $this->errorResponse('You can\'t access this product',404);
        }






    }
}
