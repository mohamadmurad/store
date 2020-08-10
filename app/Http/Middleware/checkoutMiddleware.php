<?php

namespace App\Http\Middleware;

use App\Offers;
use App\Products;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $products_price_temp = 0;
        if ($request->isJson()){
            $data = $request->json();

            if ($data->has('products')){

                $products = $data->get('products');
                foreach ($products as $product){
                    if (isset($product['product_id'])){

                        $product_in_db = Products::findOrFail((int)$product['product_id']);
                        $checkout_quantity = (int)$product['quantity'];

                        $products_price_temp+= ($product_in_db->price * $checkout_quantity);

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


                        $offer_in_db = Offers::with('products')->findOrFail((int)$offer['offer_id']);


                        $checkout_quantity = $offer['quantity'];

                        $products_price_temp+= ($offer_in_db->price * $checkout_quantity);

                        $offer_products = $offer_in_db->products;

                        foreach ($offer_products as $offer_product){
                            $product_quantity_in_db = $offer_product->quantity;
                            $product_quantity_in_offer = $offer_product->pivot->quantity;
                            $product_quantity_for_user = $product_quantity_in_offer * $checkout_quantity;


                            if ($product_quantity_in_db < $product_quantity_for_user){
                                return $this->errorResponse('Offer product ' . $offer_product->name  . ' have only ' . $product_quantity_in_db ,422);
                            }

                        }

                    }else{
                        return $this->errorResponse('Please send offer ids',422);
                    }

                }


            }


            if ($data->has('userInfo')){
                $user = Auth::user();
                $userInfo = $data->get('userInfo');

                // check password
                if (isset($userInfo['password'])){
                    if(!Hash::check($userInfo['password'], $user->password)){
                        return $this->errorResponse([
                            'message'=> 'your password incorrect',
                            'code'=> 422],
                            422);
                    }
                }else{
                    return $this->errorResponse([
                        'message'=> 'password required',
                        'code'=> 422],
                        422);
                }

                // check card pin and balance
                if (isset($userInfo['card'])){

                    if($user->card->code !== $userInfo['card']){
                        return $this->errorResponse([
                            'message'=> 'your card id incorrect',
                            'code'=> 422],
                            422);
                    }
                    //dd($products_price_temp);
                    if($user->card->balance < $products_price_temp){
                        return $this->errorResponse([
                            'message'=> 'your card have only ( ' . $user->card->balance . ' ) less than order price ( '. $products_price_temp .' )',
                            'code'=> 422],
                            422);
                    }
                }else{
                    return $this->errorResponse([
                        'message'=> 'card pin required',
                        'code'=> 422],
                        422);
                }

            }


            return $next($request);
        }else{
            return $this->errorResponse('Please send json data',422);
        }

    }


}
