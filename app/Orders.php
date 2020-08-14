<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{

    //use SoftDeletes;
    protected $fillable = ['date','discount','delevareAmount','user_id','branch_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function coupon(){
        return $this->belongsTo(Coupons::class);
    }

    public function branch(){
        return $this->belongsTo(Branches::class,'branch_id');
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

    public function scopeDate($query,$request){
        $date = $request->get('date');

        if (!empty($date)){

            return $query->whereDate('date','=', $date);
        }
    }
}
