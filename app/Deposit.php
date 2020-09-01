<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deposit extends Pivot
{

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
    protected $fillable = [
        'cost',
        'amount',
        'cards_id',
        'user_id',
        'depositDate',
    ];

    protected $dates = [
        'depositDate',
    ];


    public function admin(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function card(){
        return $this->belongsTo(Cards::class,'cards_id');
    }


    public function scopeFilterData($query,$request){
        $columns = ['user_id','cards_id'];

        $date = $request->get('date');

        if (!empty($date)){
            $query->whereDate('depositDate','=', $date);
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
