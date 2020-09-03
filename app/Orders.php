<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{

    //use SoftDeletes;
    protected $fillable = ['date','discount','delevareAmount','user_id','branch_id','price'];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function coupon(){
        return $this->belongsTo(Coupons::class);
    }

    public function branch(){
        return $this->belongsTo(Branches::class,'branch_id')->with('company');
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

    public function scopeFilterData($query,$request){
        $columns = ['user_id','branch_id'];
        $date = $request->get('date');
        if (!empty($date)){
            $query->whereDate('date','=', $date);
        }


        foreach ($columns as $column){
            $col_request = $request->get($column);

            if (!empty($col_request)){
                $query->where($column,'=', $col_request);
            }
        }


        return $query;
    }
}
