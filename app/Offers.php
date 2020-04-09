<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offers extends Model
{
    public function products(){
        return $this->belongsToMany(Products::class);
    }
}
