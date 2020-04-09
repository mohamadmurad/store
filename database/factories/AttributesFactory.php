<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AttachmentType;
use App\Attributes;
use App\Products;
use Faker\Generator as Faker;

$factory->define(Attributes::class, function (Faker $faker) {
    $types = [
        'company',
        'brand',
    ];
    $rand = $faker->unique()->randomElement($types);

    return [
        'name' => $rand,
    ];
});
