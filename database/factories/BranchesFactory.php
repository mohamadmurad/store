<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Branches;
use App\Companies;
use App\User;
use Faker\Generator as Faker;

$factory->define(Branches::class, function (Faker $faker) {
    $company = Companies::all()->random();
    $company_name = str_replace(' ','_',$company->name);
    return [
        'name' => $company_name . '_' . $faker->word(),
        'location'=> $faker->address(),
        'phone' => $faker->phoneNumber(),
        'company_id' => $company->id,

        'user_id' => $faker->unique()->numberBetween(1,100),
    ];
});
