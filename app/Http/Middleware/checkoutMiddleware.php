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
        $products_price = 0;
        if ($request->isJson()){
            $data = $request->json();

            if ($data->has('products')){
                $products = $data->get('products');
                foreach ($products as $product){
                    if (isset($product['product_id'])){

                        $product_in_db = Products::findOrFail((int)$product['product_id']);



                        $products_price+= $product_in_db->price;
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

                        $offer_in_db = Offers::with('products')->where('id','=',(int)$offer['offer_id'])->first();

                        $products_price+= $offer_in_db->price;
                        $checkout_quantity = $offer['quantity'];

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

            $user = Auth::user();
            if ($data->has('userInfo')){
                $userInfo = $data->get('userInfo');


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

                if (isset($userInfo['card'])){
//                    dd($userInfo['card']);
                    if($user->card->code !== $userInfo['card']){
                        return $this->errorResponse([
                            'message'=> 'your card id incorrect',
                            'code'=> 422],
                            422);
                    }

                    if($user->card->balance < $products_price){
                        return $this->errorResponse([
                            'message'=> 'your card balance less than order price',
                            'code'=> 422],
                            422);
                    }
                }else{
                    return $this->errorResponse([
                        'message'=> 'password required',
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
