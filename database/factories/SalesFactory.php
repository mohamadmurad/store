<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Products;
use App\Sales;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(Sales::class, function (Faker $faker) {
    $product = Products::all()->random();
    $saleRate = rand(10,70);

    $rand = rand(1,0);
    $start = null;
    $rand === 1 ? $start = Carbon::today() : $start = Carbon::today()->subDays(5);

    return [
        'saleRate' => $saleRate,
        'newPrice' => $saleRate * $product->price / 100,
        'start' =>$start,
        'end' =>$start->addDays(3),
        'product_id' =>$product->id,
    ];
});
