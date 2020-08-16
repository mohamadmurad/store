<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

    protected static function booted()
    {
        parent::booted(); // TODO: Change the autogenerated stub


//        static::addGlobalScope('status', function (Builder $builder) {
//            $builder->where('status', '=', self::AVAILABEL_PRODUCT);
//        });
    }

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        self::updating(function ($product){
           if($product->quantity < 1){
               $product->status = Products::UNAVAILABEL_PRODUCT;
           }

           $offers = $product->offers()->get();

           foreach ($offers as $offer){

               if($product->quantity < $offer->pivot->quantity){
                   $offer->end = Carbon::now()->subMinute();

                   $offer->save();
               }

           }



       });
        self::deleting(function ($product){
            foreach ($product->children()->get() as $child){

                $child->parent_id = null;
                $child->save();
            }

            $product->sales()->delete();

            $product->offers()->detach();

            $product->favorite()->detach();

            $product->orders()->detach();

            $product->attributes()->detach();

            $product->attachments->each->delete();


        });
    }

    public function attachments(){

        return $this->hasMany(Attachment::class,'products_id');
    }

    public function firstAttachments() {

        $imageId = AttachmentType::where('type','like','%image%')->pluck('id');

        return $this->hasOne(Attachment::class)->whereIn('attachmentType_id',$imageId);
    }

    public function branch(){
        return $this->belongsTo(Branches::class);
    }

    public function offers(){
        return $this->belongsToMany(Offers::class)->withPivot(['quantity']);
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

        return $this->hasOne(Sales::class,'product_id')->whereDate('end','>',Carbon::now());
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


    public function scopeAvailable($query){
        return $query->where('status', '=', self::AVAILABEL_PRODUCT)->where('quantity','>',0);
    }

    public function scopeFilterData($query,$request){
        $columns = ['status','price'];
        /*$date = $request->get('date');
        if (!empty($date)){
            $query->whereDate('date','=', $date);
        }*/

        foreach ($columns as $column){
            $col_request = $request->get($column);

            if (!empty($col_request)){
                $query->where($column,'=', $col_request);
            }
        }


        return $query;
    }
}
