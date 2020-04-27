<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'saleRate',
        'newPrice',
        'start',
        'end',
        'product_id',
    ];
    public function product(){
        return $this->belongsTo(Products::class);
    }
}
