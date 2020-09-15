<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use function foo\func;

class Branches extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'name',
        'location',
        'phone',
        'company_id',
        'user_id',
        'balance',
    ];


    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        static::deleting(function ($branch){



            $branch->products->each->delete();


        });
    }


    public function company(){
        return $this->belongsTo(Companies::class);
    }

    public function products(){
        return $this->hasMany(Products::class,'branch_id');
    }

    public function orders(){
        return $this->hasMany(Orders::class,'branch_id');
    }

    public function attributes(){
        return $this->belongsToMany(Attributes::class);
    }

    public function employee(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function branchExpense(){
        return $this->belongsToMany(User::class,'branch_expense')->withPivot(['amount','expenseDate']);
    }

    public function scopeAvailable($query){


        return $query->where('status', '=', self::AVAILABEL_PRODUCT)->where('quantity','>',0);
    }

}
