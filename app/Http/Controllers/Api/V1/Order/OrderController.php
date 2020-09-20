<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Branches;

use App\Cards;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Offers;
use App\Orders;
use App\Products;
use App\Traits\ApiResponser;
use App\User;
use Carbon\Carbon;
use Cassandra\Exception;
use Illuminate\Auth\Events\Validated;
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
        $withRelations = ['branch.company', 'products.attachments', 'offers', 'user'];

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


    public function checkout2(Request $request)
    {

        $data = $request->json();
        $orders = collect();

        $CheckoutProducts = $data->has('products') ? $data->get('products') : [];
        $CheckoutOffers = $data->has('offers') ? $data->get('offers') : [];


        DB::beginTransaction();
        try {
            $customer = Auth::user();

            //  $customer = User::whereId($customer->id)->lockForUpdate()->first();


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
                    $customer_card = $customer->card()->lockForUpdate()->first();
                    if ($customer_card->code !== $userInfo['card']) {
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


                    if (count($product_in_db->sales()->get())) {
                        // has sele
                        $products_prices_temp = $product_in_db->sales()->lockForUpdate()->first()->newPrice * (int)$product_quantity;
                    } else {
                        $products_prices_temp = $product_in_db->price * (int)$product_quantity;
                    }

                    $branch_id = $product_in_db->branch_id;

                    $temp_order = null;
                    if ($orders->has($branch_id)) {

                        $temp_order = $orders->get($branch_id);
                        $temp_order->price = $temp_order->price + $products_prices_temp;
                        $temp_order->save();

                    } else {
                        $temp_order = Orders::create([
                            'date' => Carbon::now(),
                            'price' => $products_prices_temp,
                            'discount' => 0,
                            'delevareAmount' => 0,
                            'user_id' => $customer->id,
                            'branch_id' => $branch_id,

                        ]);
                        //  $temp_order->lockForUpdate();
                        $orders->offsetSet($branch_id, $temp_order);
                    }


                    $update1 = Products::whereId($product_id)
                        ->where('updated_at', '=', $product_in_db->updated_at)
                        ->update(['quantity' => $product_in_db->quantity - (int)$product_quantity]);

                    if (!$update1) {

                        DB::rollBack();


                        return $this->errorResponse('another transaction work in product ' . $product_in_db->id, 422);
                    }


                    $temp_order->products()->attach($product_id, [
                        'quantity' => (int)$product_quantity,
                    ]);


                    $update2 = $this->transactionMoney($branch_id, $customer, $products_prices_temp);

                    if ($update2 === 0) {

                        DB::rollBack();

                        return $this->errorResponse('another transaction work in cards ', 422);
                    }

                } else {
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

                    $offer_products = $offer_in_db->products()->lockForUpdate()->get();
                    //  dd($offer_products);
                    /*foreach ($offer_products as $offer_product) {
                        $product_quantity_in_db = $offer_product->quantity;
                        $product_quantity_in_offer = $offer_product->pivot->quantity;
                        $product_quantity_for_user = $product_quantity_in_offer * $offer_quantity;
                        if ($product_quantity_in_db < $product_quantity_for_user) {
                            DB::rollBack();
                            $product_in_db = Products::available()->findOrFail($offer_product->id);
                            return $this->errorResponse('Offer product ' . $product_in_db->name . ' have only ' . $product_in_db->quantity, 422);
                        }

                    }*/

                    $branch_id = $offer_in_db->products()->first()->branch_id;

                    $offer_price_temp = (int)$offer_in_db->price * (int)$offer_quantity;


                    $temp_order = null;

                    if ($orders->has($branch_id)) {
                        $temp_order = $orders->get($branch_id);
                        $temp_order->price = $temp_order->price + $offer_price_temp;
                        $temp_order->save();
                    } else {
                        $temp_order = Orders::create([
                            'date' => Carbon::now(),
                            'discount' => 0,
                            'delevareAmount' => 0,
                            'user_id' => $customer->id,
                            'branch_id' => $branch_id,
                            'price' => $offer_price_temp,
                        ]);

                        // $temp_order->lockForUpdate();

                        $orders->offsetSet($branch_id, $temp_order);
                    }


                    // $offer_products = $offer_in_db->products()->lockForUpdate()->get();

                    foreach ($offer_products as $o_p) {
                        $product_in_offer_quantity = $o_p->pivot->quantity;
                        $all_product_quantity_in_order = $product_in_offer_quantity * $offer_quantity;

                        $update = Products::whereId($o_p->id)
                            ->where('updated_at', '=', $o_p->updated_at)
                            ->update(['quantity' => $o_p->quantity - (int)$all_product_quantity_in_order]);

                        if (!$update) {
                            DB::rollBack();
                          //  throw new \Exception('dsd');

                            return $this->errorResponse('another transaction work in product ' . $o_p->id, 22);
                        }


                    }

                    $temp_order->offers()->attach($offer_id, [
                        'quantity' => $offer_quantity,
                        'orders_id' => $temp_order->id,
                    ]);

                    $update = $this->transactionMoney($branch_id, $customer, $offer_price_temp);

                    if (!$update) {
                        DB::rollBack();
                        //throw new \Exception('ssssssdsd');
                        $this->errorResponse('another transaction work in cards ', 422);
                    }
                } else {
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
            return $this->errorResponse("Does not exist any " . $modelName . " with the specific id", 404);
        } catch (\PDOException $e) {
            DB::rollBack();
           // return $e->getMessage();
            return $this->errorResponse('your balance or quantity for a product is not enuf', 422);
        } catch (\Exception $e) {
            DB::rollBack();
          //  return $e;
            return $this->errorResponse('order not done. please try again', 422);
        }


    }

    private function transactionMoney($branch_id, $customer, $products_prices_temp)
    {
        $branch = Branches::findOrFail($branch_id);
        $customer_card = $customer->card()->lockForUpdate()->first();
        $update1 = Cards::whereId($customer_card->id)
            ->where('updated_at', '=', $customer_card->updated_at)
            ->update(['balance' => $customer_card->balance - (int)$products_prices_temp]);
        if (!$update1) {
            return 0;
        }

        $branch_card = $branch->employee()->first()->card()->lockForUpdate()->first();

        $update2 = Cards::whereId($branch_card->id)
            ->where('updated_at', '=', $branch_card->updated_at)
            ->update(['balance' => $branch_card->balance + (int)$products_prices_temp]);


        if (!$update2) {
            return 0;
        }

        return 1;


    }


    public function checkQuantity(Request $request){

        $rules = [
            'quantity' => 'required|integer|min:1',
            'id' => 'required|integer|exists:products,id',
        ];

        $this->validate($request,$rules);

        $quantity = $request->get('quantity');
        $id = $request->get('id');

        $product = Products::findOrFail($id)->first();
        if ($product->quantity >= $quantity){

            return $this->successResponse([
                'state' => true
            ],200);
        }else{
            return $this->successResponse([
                'state' => false
            ],422);
        }


    }


}
