<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Products;
use App\Rate;
use App\User;
use Faker\Generator as Faker;

$factory->define(Rate::class, function (Faker $faker) {
    $product = Products::all()->random();
    $user = User::all()->random();
    return [
        'product_id' => $product->id,
        'user_id' => $user->id,
        'rate' => rand(1,5),
    ];
});
