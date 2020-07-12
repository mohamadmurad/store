<?php

namespace App\Http\Middleware;

use App\Offers;
use App\Products;
use App\Traits\ApiResponser;
use Closure;

class checkoutMiddleware
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

        if ($request->isJson()){
            $data = $request->json();

            if ($data->has('products')){
                $products = $data->get('products');
                foreach ($products as $product){
                    if (isset($product['product_id'])){

                        $product_in_db = Products::findOrFail((int)$product['product_id'])->first();
                        $checkout_quantity = (int)$product['quantity'];
                        if ($product_in_db->quantity < $checkout_quantity){
                            return $this->errorResponse('product ' . $product_in_db->name  . ' have only ' . $product_in_db->quantity ,422);
                        }

                    }else{
                        return $this->errorResponse('Please send product ids',422);
                    }

                }
            }



            if ($data->has('offers')){
                $offers = $data->get('offers');
                foreach ($offers as $offer){

                    if (isset($offer['offer_id'])){

                        $offer_in_db = Offers::findOrFail((int)$offer['offer_id'])->with('products')->first();
                        $checkout_quantity = (int)$offer['quantity'];

                        $offer_products = $offer_in_db->products;
                        foreach ($offer_products as $offer_product){
                            $product_quantity_in_db = $offer_product->quantity;
                            $product_quantity_in_offer = $offer_product->pivot->quantity;
                            $product_quantity_for_user = $product_quantity_in_offer * $checkout_quantity;


                            if ($product_quantity_in_db < $product_quantity_for_user){
                                return $this->errorResponse('product ' . $product_in_db->name  . ' have only ' . $product_quantity_in_db ,422);
                            }

                        }




                    }else{
                        return $this->errorResponse('Please send offer ids',422);
                    }

                }
            }


            return $next($request);
        }else{
            return $this->errorResponse('Please send json data',422);
        }

    }
}
