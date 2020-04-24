<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use SoftDeletes;

    protected $with = ['type'];
    protected $fillable = [
        'src',
        'products_id',
        'attachmentType_id',
    ];

    public function product(){
        return $this->belongsTo(Products::class);
    }

    public function type(){
        return $this->belongsTo(AttachmentType::class,'attachmentType_id');
    }
}
