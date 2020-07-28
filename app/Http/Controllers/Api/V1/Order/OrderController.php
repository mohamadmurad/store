<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Controllers\Controller;
use App\Offers;
use App\Orders;
use App\Products;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Sodium\add;

class OrderController extends Controller
{

    use ApiResponser;
    public function __construct()
    {
        $this->middleware(['role:customer','checkoutMiddleware'])->only('checkout');
    }


    public function checkout(Request $request){

            $data = $request->json();
            $ordersProductByBranch = collect();
            $ordersOfferByBranch = collect();



            $OrderProducts = $data->get('products');
            $OrderOffers = $data->get('offers');


            foreach ($OrderProducts as $product){

                $product_in_db = Products::findOrFail((int)$product['product_id']);

                if ($ordersProductByBranch->has($product_in_db->branch_id)){
                    $ordersProductByBranch->offsetSet($product_in_db->branch_id,array_merge($ordersProductByBranch->get($product_in_db->branch_id),[$product['product_id']])) ;
                }else{
                    $ordersProductByBranch->offsetSet($product_in_db->branch_id,[$product['product_id']]);
                }
            }


            foreach ($OrderOffers as $offer){

                $offer_in_db = Offers::findOrFail((int)$offer['offer_id']);
                $branch_id = $offer_in_db->products()->first()->branch_id;
                if ($ordersOfferByBranch->has($branch_id)){
                    $ordersOfferByBranch->offsetSet($branch_id,array_merge($ordersOfferByBranch->get($branch_id),[$offer['offer_id']])) ;
                }else{
                    $ordersOfferByBranch->offsetSet($branch_id,[$offer['offer_id']]);
                }
            }




            $customer = Auth::user();
            foreach ($ordersProductByBranch as $key => $products){

                $order = Orders::create([
                    'date'=> Carbon::now(),
                    'discount' => 0,
                    'delevareAmount' => 0,
                    'user_id' => $customer->id,
                ]);

                foreach ($products as $product){
                    foreach ($OrderProducts as $productJson){
                        if($product === (int)$productJson['product_id']){
                            $order->products()->attach($product,[
                                    'quantity'=>$productJson['quantity'],
                            ]);

                        }
                    }

                }
                if ($ordersOfferByBranch->has($key)){
                    $offers_in_same_branch = $ordersOfferByBranch->get($key);
                     foreach($offers_in_same_branch as $offer_in){
                        foreach ($OrderOffers as $OfferJson){
                            if($offer_in === (int)$OfferJson['offer_id']){
                                $order->offers()->attach($offer_in,[
                                    'quantity'=>$OfferJson['quantity'],
                                ]);
                            }
                        }


                    }
                }


            }





    }
}
