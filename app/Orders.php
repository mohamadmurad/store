<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function coupon(){
        return $this->belongsTo(Coupons::class);
    }

    public function products(){
        return $this->belongsToMany(Products::class)->withPivot([
            'quantity',
            'name',
        ]);
    }
}
