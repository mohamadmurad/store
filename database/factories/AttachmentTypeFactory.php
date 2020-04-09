<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AttachmentType;
use Faker\Generator as Faker;

$factory->define(AttachmentType::class, function (Faker $faker) {
    $types = [
        'image',
        'video',
    ];
    $rand =$faker->unique()->randomElement($types);
    return [
        'type' => $rand,
    ];
});
