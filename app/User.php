<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'username',
        'location',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',''
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'blocked' => 'boolean',
    ];


    public function card(){
        return $this->hasOne(Cards::class);
    }

//    public function rates(){
//        return $this->hasMany(Rate::class);
//    }

    public function orders(){
        return $this->hasMany(Orders::class);
    }

    public function branch(){
        return $this->hasOne(Branches::class);
    }

    public function favorite(){
        return $this->belongsToMany(Products::class,'favorite')
            ->as('favorite')
            ->withTimestamps();
    }


}
