<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cards;
use App\User;
use Faker\Calculator\Luhn;
use Faker\Generator as Faker;

$factory->define(Cards::class, function (Faker $faker) {


    $pin = $faker->unique()->numberBetween(100000,999);

    return [
        'code' =>Cards::randomCardCode(true),
        'pin'=> Cards::randomCardPin(),
        'balance' => $faker->numberBetween(1000,100000),
        //'user_id' =>$user->id,
    ];
});



