<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Coupons;
use Faker\Generator as Faker;

$factory->define(Coupons::class, function (Faker $faker) {


    return [
        'code' => Coupons::randomCouponCode(),
        'discountRate'=> rand(3,90),
    ];
});
