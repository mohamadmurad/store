<?php

namespace App;

use App\Traits\ApiResponser;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable,SoftDeletes, HasApiTokens;

    use HasRoles, ApiResponser;
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


    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::deleting(function ($user){
            $branch = $user->branch()->first();
            if ($branch != null){
            //    throw new Exception('this User Has A branch please delete branch and try again',422);

                return response('dfsfs');
            }


        });
    }


    public function card(){
        return $this->hasOne(Cards::class);
    }


    public function CardCharge(){
        return $this->belongsToMany(Cards::class,'CardCharge')->withPivot(['amount','chargeDate']);
    }


    public function branchExpense(){
        return $this->belongsToMany(Branches::class,'branch_expense')->withPivot(['amount','expenseDate']);
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
