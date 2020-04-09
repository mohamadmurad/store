<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Categories;
use Faker\Generator as Faker;

$factory->define(Categories::class, function (Faker $faker) {
   // $parent = Categories::all()->random();
    $name = $faker->unique()->word();
    return [
        'name' => $name,
       // 'parent_id' =>$parent->id,
    ];
});
