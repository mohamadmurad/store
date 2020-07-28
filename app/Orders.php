<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{

    use SoftDeletes;
    protected $fillable = ['date','discount','delevareAmount','user_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function coupon(){
        return $this->belongsTo(Coupons::class);
    }

    public function products(){
        return $this->belongsToMany(Products::class)->withPivot([
            'quantity',
        ]);
    }

    public function offers(){
        return $this->belongsToMany(Offers::class)->withPivot([
            'quantity',
        ]);
    }
}
