<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Favorate;
use Faker\Generator as Faker;

$factory->define(Favorate::class, function (Faker $faker) {
    $product = Products::all()->random();
    $user = User::all()->random();
    return [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ];
});
