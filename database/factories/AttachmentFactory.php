<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Attachment;
use App\AttachmentType;
use Faker\Generator as Faker;

$factory->define(Attachment::class, function (Faker $faker) {
    $attachmentType = AttachmentType::all()->random();
    return [
        'src' => '1.png',
        //'product_id' =>$product->id,
        'attachmentType_id' =>$attachmentType->id,
    ];
});
