<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Branches;
use App\Exceptions\checkoutException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Offers;
use App\Orders;
use App\Products;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Cassandra\Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class OrderController extends Controller
{

    use ApiResponser;

    public function __construct()
    {
        $this->middleware(['role:customer'])->only('checkout2');

    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $withRelations = ['branch', 'products.attachments', 'offers', 'user'];

        if ($user->hasRole('customer')) {
            $orders = $user->orders()->with($withRelations)->get();
            return $this->showCollection(OrderResource::collection($orders));
        }

        if ($user->hasRole('employee') || $user->hasRole('super_employee')) {
            $branch = $user->branch()->first();
            $orders = $branch->orders()->with($withRelations)->get();
            return $this->showCollection(OrderResource::collection($orders));
        }

        $orders = Orders::FilterData($request)->with($withRelations)->get();


        return $this->showCollection(OrderResource::collection($orders));

    }

    public function show(Orders $order)
    {

        $employee = Auth::user();
        if ($employee->hasRole('customer')) {
            if ($order->user_id != $employee->id) {

                return $this->errorResponse('you cant access this order', 403);
            }
        }

        if ($employee->hasRole('employee') || $employee->hasRole('super_employee')) {
            if ($order->branch_id != $employee->branch()->first()->id) {

                return $this->errorResponse('you cant access this order', 403);
            }
        }
        return $this->showModel(new OrderResource($order->load('products.attachments')));


    }

    public function checkout(Request $request)
    {

        $data = $request->json();
        $ordersProductByBranch = collect();
        $ordersOfferByBranch = collect();


        $OrderProducts = $data->has('products') ? $data->get('products') : [];
        $OrderOffers = $data->has('offers') ? $data->get('offers') : [];


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
                $offer_price_temp = 0;

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
                                $offer_price_temp += (int)$offer_in_db->price * (int)$OfferJson['quantity'];

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
            foreach ($ordersOfferByBranch as $key => $offers) {
                $offer_price_temp = 0;
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
                            $offer_price_temp += (int)$offer_in_db->price * (int)$OfferJson['quantity'];;

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

                $branch = Branches::findOrFail($key);
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
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorResponse('order not done. please try again', 422);

        }


    }

    public function checkout2(Request $request)
    {

        $data = $request->json();
        $orders = collect();

        $CheckoutProducts = $data->has('products') ? $data->get('products') : [];
        $CheckoutOffers = $data->has('offers') ? $data->get('offers') : [];


        DB::beginTransaction();
        try {
            $customer = Auth::user();

            if ($data->has('userInfo')) {

                $userInfo = $data->get('userInfo');

                // check password
                if (isset($userInfo['password'])) {
                    if (!Hash::check($userInfo['password'], $customer->password)) {
                        DB::rollBack();
                        return $this->errorResponse([
                            'message' => 'your password incorrect',
                            'code' => 422],
                            422);
                    }
                } else {
                    DB::rollBack();
                    return $this->errorResponse([
                        'message' => 'password required',
                        'code' => 422],
                        422);
                }

                // check card pin and balance
                if (isset($userInfo['card'])) {

                    $customer->card()->lockForUpdate();


                    if ($customer->card->code !== $userInfo['card']) {
                        DB::rollBack();
                        return $this->errorResponse([
                            'message' => 'your card id incorrect',
                            'code' => 422],
                            422);
                    }

                    /*if ($customer->card->balance < $products_price_temp) {
                        return $this->errorResponse([
                            'message' => 'your card have only ( ' . $user->card->balance . ' ) less than order price ( ' . $products_price_temp . ' )',
                            'code' => 422],
                            422);
                    }*/

                } else {
                    DB::rollBack();
                    return $this->errorResponse([
                        'message' => 'card pin required',
                        'code' => 422],
                        422);
                }

            }


            // make orders for all products
            foreach ($CheckoutProducts as $product) {
                if (isset($product['id'])) {

                    $product_id = (int)$product['id'];
                    $product_quantity = (int)$product['quantity'];
                    $product_in_db = Products::available()->lockForUpdate()->findOrFail($product_id);

                    // check quantity
                    if ($product_in_db->quantity < $product_quantity) {
                        DB::rollBack();
                        $product_in_db = Products::available()->findOrFail($product_id);
                        return $this->errorResponse('product ' . $product_in_db->name . ' have only ' . $product_in_db->quantity, 422);
                    }

                    $branch_id = $product_in_db->branch_id;

                    $temp_order = null;
                    if ($orders->has($branch_id)) {
                        $temp_order = $orders->get($branch_id);
                    } else {
                        $temp_order = Orders::create([
                            'date' => Carbon::now(),
                            'discount' => 0,
                            'delevareAmount' => 0,
                            'user_id' => $customer->id,
                            'branch_id' => $branch_id,
                        ]);

                        $orders->offsetSet($branch_id, $temp_order);
                    }


                    $products_prices_temp = $product_in_db->price * (int)$product_quantity;


                    $product_in_db->quantity = $product_in_db->quantity - (int)$product_quantity;

                    $product_in_db->save();
                    $temp_order->products()->attach($product_id, [
                        'quantity' => (int)$product_quantity,
                    ]);

                    $this->transactionMoney($branch_id, $customer, $products_prices_temp);
                }else{
                    DB::rollBack();
                    return $this->errorResponse('Please send product ids', 422);

                }

            }

            // make orders for all offer
            foreach ($CheckoutOffers as $offer) {
                if (isset($offer['id'])) {

                    $offer_id = (int)$offer['id'];
                    $offer_quantity = (int)$offer['quantity'];
                    $offer_in_db = Offers::lockForUpdate()->findOrFail($offer_id);

                    $offer_products = $offer_in_db->products;
                    foreach ($offer_products as $offer_product) {
                        $product_quantity_in_db = $offer_product->quantity;
                        $product_quantity_in_offer = $offer_product->pivot->quantity;
                        $product_quantity_for_user = $product_quantity_in_offer * $offer_quantity;
                        if ($product_quantity_in_db < $product_quantity_for_user) {
                            DB::rollBack();
                            $product_in_db = Products::available()->findOrFail($offer_product->id);
                            return $this->errorResponse('Offer product ' . $product_in_db->name . ' have only ' . $product_in_db->quantity, 422);
                        }

                    }

                    $branch_id = $offer_in_db->products()->first()->branch_id;
                    $temp_order = null;
                    if ($orders->has($branch_id)) {
                        $temp_order = $orders->get($branch_id);
                    } else {
                        $temp_order = Orders::create([
                            'date' => Carbon::now(),
                            'discount' => 0,
                            'delevareAmount' => 0,
                            'user_id' => $customer->id,
                            'branch_id' => $branch_id,
                        ]);

                        $orders->offsetSet($branch_id, $temp_order);
                    }


                    $offer_price_temp = (int)$offer_in_db->price * (int)$offer_quantity;

                    $offer_products = $offer_in_db->products()->lockForUpdate()->get();
                    foreach ($offer_products as $o_p) {
                        $product_in_offer_quantity = $o_p->pivot->quantity;
                        $all_product_quantity_in_order = $product_in_offer_quantity * $offer_quantity;
                        $o_p->quantity -= $all_product_quantity_in_order;
                        $o_p->save();
                    }

                    $temp_order->offers()->attach($offer_id, [
                        'quantity' => $offer_quantity,
                    ]);

                    $this->transactionMoney($branch_id, $customer, $offer_price_temp);
                }else{
                    DB::rollBack();
                   return $this->errorResponse('Please send offer ids', 422);
                }


            }

            DB::commit();

            return $this->successResponse([
                'message' => 'orders save successful',
                'code' => 201,
            ], 201);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            $modelName = strtolower(class_basename($e->getModel()));
            return $this->errorResponse("Does not exist any ". $modelName ." with the specific id",404);
        }catch (\PDOException $e) {
            DB::rollBack();
            return $this->errorResponse('your balance or quantity for a product is not enuf', 422);
        }catch (\Exception $e) {
            DB::rollBack();
            return $e;
            return $this->errorResponse('order not done. please try again', 422);
        }


    }

    private function transactionMoney($branch_id, $customer, $products_prices_temp)
    {
        $branch = Branches::findOrFail($branch_id);
        $customer_card = $customer->card()->first();


        $customer_card->balance -= $products_prices_temp;
        $branch_card = $branch->employee()->first()->card()->lockForUpdate()->first();

        $branch_card->balance += $products_prices_temp;

        $branch_card->save();

        $customer_card->save();
    }


    private function checkUserInfoForCheckout($data, $products_price_temp)
    {



        return null;

    }


}
