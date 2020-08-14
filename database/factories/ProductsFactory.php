<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Categories;
use App\Groups;
use App\Products;
use Faker\Generator as Faker;

$factory->define(Products::class, function (Faker $faker) {
    $product = $faker->unique()->sentence;
    $latinName = $faker->unique()->sentence;
    return [
        'name' => $product,
        'latinName' => $latinName,
        'quantity' => $faker->numberBetween(1,10),
        'status' => $faker->randomElement([products::UNAVAILABEL_PRODUCT,products::AVAILABEL_PRODUCT]),
        'price' => $faker->numberBetween(1000,1500),
        'details' => $faker->paragraph(1),
        'code' => $faker->uuid,
        'branch_id' => 1,
       // 'parent_id' => null,
        'category_id' => 1,
       // 'group_id' => null,
    ];
});
