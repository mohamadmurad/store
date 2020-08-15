<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Offers;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(Offers::class, function (Faker $faker) {
    $rand = rand(1,0);
    $start = null;
    $rand === 1 ? $start = Carbon::now() : $start = Carbon::today()->subDay();

    return [
        'number' => $faker->uuid,
        'price' => $faker->numberBetween(100,1000),
        'start' => $start,
        'end' => $start->addDays(3),

    ];
});
