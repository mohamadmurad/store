<?php

namespace App\Http\Resources\Attachment;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        if ($request->has('web')){
            return [
                'id'=>$this->id,
                'big' => $request->getSchemeAndHttpHost() . '/'. config('app.PRODUCTS_FILES_PATH','files/products/') .$this->src,
                'thumb' => $request->getSchemeAndHttpHost() . '/'. config('app.PRODUCTS_FILES_PATH','files/products/') .$this->src,
                'path' => $request->getSchemeAndHttpHost() . '/'. config('app.PRODUCTS_FILES_PATH','files/products/') .$this->src,
                //'type' => $this->type->type,
            ];
        }
        return [
            'path' => $request->getSchemeAndHttpHost() . '/'. config('app.PRODUCTS_FILES_PATH','files/products/') .$this->src,
            'type' => $this->type->type,
        ];
    }
}
