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


        if ($request->route()->hasParameter('employee_product')){

            $product_branch_id = $request->route()->parameter('employee_product')->branch_id;
            if ((int)$product_branch_id === (int)$branch_id){

                return $next($request);
            }else{

                return $this->errorResponse('You can\'t access this product',404);
            }


        }elseif($request->isJson()){
            $data = $request->json();
            // for add offer

            if($data->has('products')){

                $products = $data->get('products');

                foreach ($products as $product){
                    if (isset($product['id'])){
                        $product_branch_id = Products::findOrFail((int)$product['id'])->branch_id;
                        if ((int)$product_branch_id !== (int)$branch_id){
                            return $this->errorResponse('You can\'t access this product',404);

                        }
                    }else{
                        return $this->errorResponse('Please send product ids',422);
                    }



                }

                return $next($request);

            }else{
                return $this->errorResponse('Please send products array',422);
            }

        }

        return abort(403,'access denied');

    }
}
