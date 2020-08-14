<?php

use App\Branches;
use App\Offers;
use App\Orders;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FixedOrders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer = User::all()->where('username','like','meheden')->first();
        $branch =  User::all()->where('username','like','Hasan')->first()->branch()->first();


        $newOffer = Offers::create([
            'price' => 1000,
            'number' => Offers::randomOfferNumber(),
            'start' => Carbon::now(),
            'end' => Carbon::now()->addDays(2),
        ]);
        $product_id = $branch->products()->first();
        $newOffer->products()->attach($product_id->id,['quantity'=>$product_id->quantity-1]);

        $product_id = $branch->products()->get();
        $newOffer->products()->attach($product_id[1]->id,['quantity'=>$product_id[1]->quantity-1]);


        $order = Orders::create([
            'date' => Carbon::now(),
            'discount' => 0,
            'delevareAmount' => 0,
            'user_id' => $customer->id,
            'branch_id' => $branch->id,
        ]);
        $product = $branch->products()->limit(2);
        $order->products()->attach($product->pluck('id'), [
            'quantity' => rand(1,2),
        ]);

        $order->offers()->attach($newOffer->id, [
            'quantity' => rand(1,2),
        ]);

$prices = 0;
foreach ($product->get() as $p){

        $prices+= $p->price;
}

        $customer_card = $customer->card()->first();
        $total_price_to_branch = $newOffer->price + $prices;
        $customer_card->balance -= $total_price_to_branch;
        $branch_card = $branch->employee()->first()->card()->first();
        $branch_card->balance += $total_price_to_branch;
        $branch_card->save();
        $customer_card->save();


        $branch =  Branches::all()->random(1)->first();
        $order = Orders::create([
            'date' => Carbon::now(),
            'discount' => 0,
            'delevareAmount' => 0,
            'user_id' => $customer->id,
            'branch_id' => $branch->id,
        ]);


        $product = $branch->products()->limit(1);
        $order->products()->attach($product->pluck('id'), [
            'quantity' => rand(1,2),
        ]);

        $customer_card = $customer->card()->first();
        $total_price_to_branch = 0+ $product->first()->price;
        $customer_card->balance -= $total_price_to_branch;
        $branch_card = $branch->employee()->first()->card()->first();
        $branch_card->balance += $total_price_to_branch;
        $branch_card->save();
        $customer_card->save();


    }
}
