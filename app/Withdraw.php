<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{

    protected $table = 'withdraw';
    public $incrementing = true;
    protected $fillable = [
        'amount',
        'cards_id',
        'user_id',
        'withdrawDate',
    ];

    protected $dates = [
        'withdrawDate',
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
            $query->whereDate('withdrawDate','=', $date);
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
