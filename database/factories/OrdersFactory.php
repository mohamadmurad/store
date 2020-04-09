<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Orders;
use App\Products;
use App\User;
use Faker\Generator as Faker;

$factory->define(Orders::class, function (Faker $faker) {
    $user = User::all()->random();
    return [
        'date'=> now(),
        'discount' => 0.0,
        'delevareAmount' => 0.0,
        'user_id' => $user->id,
        'coupon_id' => null,

    ];
});
