<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branches extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'name',
        'location',
        'company_id',
        'user_id',
        'balance',
    ];


    public function company(){
        return $this->belongsTo(Companies::class);
    }

    public function products(){
        return $this->hasMany(Products::class,'branch_id');
    }

    public function attributes(){
        return $this->belongsToMany(Attributes::class);
    }

    public function employee(){
        return $this->hasOne(User::class);
    }
}
