<?php

namespace App;

use Faker\Calculator\Luhn;
use Faker\Provider\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupons extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'code',
        'discountRate',
    ];

    public function orders(){
        return $this->hasMany(Orders::class);
    }

    /**
     * Return the String of a SWIFT/BIC number
     *
     * @example 'RZTIAT22263'
     * @link    http://en.wikipedia.org/wiki/ISO_9362
     * @return  string Swift/Bic number
     */
    public static function randomCouponCode(){
        return Base::regexify("^([A-Z]){2}([0-9]){2}([A-Z]){2}");
    }
}
