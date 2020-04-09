<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use SoftDeletes;

    public function product(){
        return $this->belongsTo(Products::class);
    }

    public function attachmentType(){
        return $this->belongsTo(AttachmentType::class);
    }
}
