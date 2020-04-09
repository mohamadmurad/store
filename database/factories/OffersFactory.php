<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Offers;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(Offers::class, function (Faker $faker) {
    $rand = rand(1,0);
    $start = null;
    $rand === 1 ? $start = Carbon::today() : $start = Carbon::today()->subDay();

    return [
        'number' => $faker->uuid,
        'price' => $faker->randomFloat(2,100,10000),
        'start' => $start,
        'end' => $start->addDays(3),

    ];
});
