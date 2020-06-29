<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use function foo\func;

class Companies extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'name',
        'phone',
    ];


    public function branches(){
        return $this->hasMany(Branches::class,'company_id');
    }
}
