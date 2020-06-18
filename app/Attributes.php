<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attributes extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'name',
    ];

    public function branches(){
        return $this->belongsToMany(Branches::class)->as('attribute_values');
    }

    public function products(){
        return $this->belongsToMany(Products::class,'attribute_values')
            ->as('attribute_values')
            ->withPivot([
                'value',
            ]);
    }
}
