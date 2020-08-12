<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class CardCharge extends Pivot
{
    use SoftDeletes;
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
        'chargeDate',
    ];

    protected $dates = [
        'chargeDate',
    ];


    public function admin(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function card(){
        return $this->belongsTo(Cards::class,'cards_id');
    }

}
