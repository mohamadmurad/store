<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Branches;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Offers;
use App\Orders;
use App\Products;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use http\Env\Response;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{

    use ApiResponser;

    public function __construct()
    {
        $this->middleware(['role:customer', 'checkoutMiddleware'])->only('checkout');
        $this->middleware(['role:customer'])->only('myOrders');
        $this->middleware(['role:employee|super_employee'])->only('branchOrders');
    }


    public function checkout(Request $request)
    {

        $data = $request->json();
        $ordersProductByBranch = collect();
        $ordersOfferByBranch = collect();


        $OrderProducts = $data->has('products') ? $data->get('products') : [];
        $OrderOffers = $data->has('offers')  ? $data->get('offers') : [];


        DB::beginTransaction();
        try {

            // add products to map  And delete quantity
            foreach ($OrderProducts as $product) {

                $product_id = (int)$product['id'];
                $product_in_db = Products::findOrFail($product_id);

                $branch_id = $product_in_db->branch_id;

                if ($ordersProductByBranch->has($branch_id)) {
                    $ordersProductByBranch->offsetSet($branch_id, array_merge($ordersProductByBranch->get($branch_id), [$product_id]));
                } else {
                    $ordersProductByBranch->offsetSet($branch_id, [$product_id]);
                }



            }

            // add offer to map And delete quantity
            foreach ($OrderOffers as $offer) {
                $offer_id = (int)$offer['id'];
                $offer_in_db = Offers::findOrFail($offer_id);

                $branch_id = $offer_in_db->products()->first()->branch_id;
                if ($ordersOfferByBranch->has($branch_id)) {
                    $ordersOfferByBranch->offsetSet($branch_id, array_merge($ordersOfferByBranch->get($branch_id), [$offer_id]));
                } else {
                    $ordersOfferByBranch->offsetSet($branch_id, [$offer_id]);
                }
                //$offer_products = $offer_in_db->products()->get();
//                foreach ($offer_products as $o_p) {
//                    $product_in_offer_quantity = $o_p->pivot->quantity;
//                    $all_product_quantity_in_order = $product_in_offer_quantity * $offer['quantity'];
//
//                }

            }


            $customer = Auth::user();

            // make orders
            foreach ($ordersProductByBranch as $key => $products) {
                $products_prices_temp = 0;
                $offer_price_temp =0;

                $order = Orders::create([
                    'date' => Carbon::now(),
                    'discount' => 0,
                    'delevareAmount' => 0,
                    'user_id' => $customer->id,
                    'branch_id' => $key,
                ]);


                // attach branch product to order
                foreach ($products as $product) {

                    foreach ($OrderProducts as $productJson) {
                        if ($product === (int)$productJson['id']) {

                            $product_in_db = Products::findOrFail($product);
                            $products_prices_temp += $product_in_db->price * (int)$productJson['quantity'];

                            $product_in_db->quantity = $product_in_db->quantity - (int)$productJson['quantity'];
                            $product_in_db->save();

                            $order->products()->attach($product, [
                                'quantity' => (int)$productJson['quantity'],
                            ]);

                        }
                    }

                }



                // attach branch offer to order
                if ($ordersOfferByBranch->has($key)) {

                    $offers_in_same_branch = $ordersOfferByBranch->pull($key);
                    foreach ($offers_in_same_branch as $offer_in) {
                        foreach ($OrderOffers as $OfferJson) {

                            if ($offer_in === (int)$OfferJson['id']) {

                                $offer_in_db = Offers::findOrFail($OfferJson['id']);
                                $offer_price_temp += (int)$offer_in_db->price * (int) $OfferJson['quantity'];

                                $offer_products = $offer_in_db->products()->get();
                                foreach ($offer_products as $o_p) {
                                    $product_in_offer_quantity = $o_p->pivot->quantity;
                                    $all_product_quantity_in_order = $product_in_offer_quantity * $offer['quantity'];

                                    $o_p->quantity -= $all_product_quantity_in_order;
                                    $o_p->save();

                                }


                                $order->offers()->attach($offer_in, [
                                    'quantity' => $OfferJson['quantity'],
                                ]);
                            }
                        }


                    }

                }


                // delete balance and add it to branch

                $branch = Branches::findOrFail($key);
                $customer_card = $customer->card()->first();

                $total_price_to_branch = $offer_price_temp + $products_prices_temp;
                $customer_card->balance -= $total_price_to_branch;
                $branch_card = $branch->employee()->first()->card()->first();

                $branch_card->balance += $total_price_to_branch;



                $branch_card->save();


                $customer_card->save();




            }

            // all other offer
            foreach ($ordersOfferByBranch as $key => $offers){
                $offer_price_temp =0;
                $order = Orders::create([
                    'date' => Carbon::now(),
                    'discount' => 0,
                    'delevareAmount' => 0,
                    'user_id' => $customer->id,
                    'branch_id' => $key,

                ]);

                // attach branch offer to order
                foreach ($offers as $offer) {
                    foreach ($OrderOffers as $OfferJson) {

                        if ($offer === (int)$OfferJson['id']) {

                            $offer_in_db = Offers::findOrFail($OfferJson['id']);
                            $offer_price_temp += (int)$offer_in_db->price * (int) $OfferJson['quantity'];;

                            $offer_products = $offer_in_db->products()->get();
                            foreach ($offer_products as $o_p) {
                                $product_in_offer_quantity = $o_p->pivot->quantity;
                                $all_product_quantity_in_order = $product_in_offer_quantity * $offer['quantity'];

                                $o_p->quantity -= $all_product_quantity_in_order;
                                $o_p->save();

                            }

                            $order->offers()->attach($offer_in, [
                                'quantity' => $OfferJson['quantity'],
                            ]);
                        }
                    }

                }


                // delete balance and add it to branch

                $branch= Branches::findOrFail($key);
                $customer_card = $customer->card()->first();

                $total_price_to_branch = $offer_price_temp;
                $customer_card->balance -= $total_price_to_branch;
                $branch_card = $branch->employee()->first()->card()->first();
                $branch_card->balance += $total_price_to_branch;

                $branch_card->save();

                $customer_card->save();

            }


            DB::commit();

            return $this->successResponse([
                'message' => 'orders save successful',
                'code' => 201,
            ],201);

        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorResponse('order not done. please try again', 422);

        }


    }

    public function myOrders(){

        $customer = Auth::user();
        $orders = $customer->orders()->with(['branch','products.attachments','offers'])->get();

        return $this->showCollection(OrderResource::collection($orders));

    }

    public function branchOrders(){

        $employee = Auth::user();
        $branch = $employee->branch()->first();
        $orders = $branch->orders()->with(['branch','products.attachments','offers'])->get();

        return $this->showCollection(OrderResource::collection($orders));

    }

    public function show(Orders $order){

        $employee = Auth::user();
        if ($employee->hasRole('customer')) {
            if($order->user_id != $employee->id){

                return $this->errorResponse('you cant access this order',403);
            }
        }

        if ($employee->hasRole('employee') || $employee->hasRole('super_employee')) {
            if($order->branch_id != $employee->branch()->first()->id){

                return $this->errorResponse('you cant access this order',403);
            }
        }
        return $this->showModel(new OrderResource($order->load('products.attachments')));


    }
}
