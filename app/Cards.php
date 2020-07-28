<?php

namespace App;

use Faker\Calculator\Luhn;
use Faker\Provider\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cards extends Model
{


    use SoftDeletes;
    protected $fillable = [
        'code',
        'pin',
        'balance',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function CardCharge(){
        return $this->belongsToMany(User::class,'CardCharge')->withPivot(['amount','chargeDate']);
    }


    public static function randomCardCode($formatted = false, $separator = '-'){

        $mask = "########";

        $number = Base::numerify($mask);
        $number .= Luhn::computeCheckDigit($number);

        if ($formatted) {
            $p1 = substr($number, 0, 4);
            $p2 = substr($number, 4, 4);
           // $p3 = substr($number, 8, 4);
           // $p4 = substr($number, 12);
            $number = $p1 . $separator . $p2 ;//. $separator . $p3 . $separator . $p4;
        }

        return $number;
    }

    public static function randomCardPin(){

        $mask = "#####";
        $number = Base::numerify($mask);
        $number .= Luhn::computeCheckDigit($number);
        return $number;
    }
}
