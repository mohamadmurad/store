<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{

    use SoftDeletes;
    const UNAVAILABEL_PRODUCT = 'unavailable';
    const AVAILABEL_PRODUCT = 'available';

   // protected $with = ['firstAttachments','sales'];

    protected $fillable =[
        'name',
        'latinName',
        'code',
        'quantity',
        'status',
        'price',
        'details',
        'branch_id',
        'parent_id',
        'category_id',
        'group_id',

    ];

    protected $casts = [
        'quantity' => 'integer',
        'status' => 'string',
        'price' => 'float',
        'details' => 'string',
        'branch_id' => 'integer',
        'parent_id'=> 'integer',
        'category_id'=> 'integer',
        'group_id'=> 'integer',
    ];



    public function attachments(){
        return $this->hasMany(Attachment::class,'products_id');
    }

    public function firstAttachments() {

        $imageId = AttachmentType::where('type','=','image')->pluck('id')->first();
        return $this->hasOne(Attachment::class)->where('attachmentType_id','=',$imageId);
    }

    public function branch(){
        return $this->belongsTo(Branches::class);
    }

    public function offers(){
        return $this->belongsToMany(Offers::class);
    }

    public function group(){
        return $this->belongsTo(Groups::class);
    }

    public function category(){
        return $this->belongsTo(Categories::class);
    }

    public function orders(){
        return $this->belongsToMany(Orders::class);
    }

    public function parent(){
        return $this->belongsTo(Products::class);
    }

    public function children(){
        return $this->hasMany(Products::class,'parent_id');
    }

    public function rates(){
        return $this->belongsToMany(User::class,'rates')
            ->as('rates')
            ->withTimestamps()

            ->withPivot([
                'rate',
            ]);
    }

    public function sales(){

        return $this->hasOne(Sales::class,'product_id')->whereDate('end','>=',Carbon::now());
    }

    public function favorite(){
        return $this->belongsToMany(User::class,'favorite');
    }

    public function attributes(){
        return $this->belongsToMany(Attributes::class,'attribute_values')
            ->as('attribute_values')
            ->withPivot([
                'value',
            ]);
    }
}
