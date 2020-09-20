<?php

namespace App\Http\Resources\product;

use App\Categories;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Http\Resources\Attribute\infoAttributeResource;
use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Sale\SaleResource;
use App\Products;
use Illuminate\Http\Resources\Json\JsonResource;

class WebProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {



        if($request->product){
            $proFromSameCat = Products::where('category_id','=',$this->category_id)->where('id','!=',$this->id)->limit(3)->get();
            return [
                'id' => $this->id,
                'name' => $this->name,
                'quantity' => $this->quantity,
                'status' => $this->status,
                'price' => (float) $this->price,
                'details' => $this->details,
                'viewed'  => $this->viewed,
                'category' => new CategoryResource(Categories::findOrFail($this->category_id)),
                'branch' => new BranchResource($this->whenLoaded('branch')),
                'media' => AttachmentResource::collection($this->whenLoaded('attachments')),
                //'image' => new AttachmentResource ($this->whenLoaded('firstAttachments')),
                'sale' => new SaleResource($this->whenLoaded('sales')),
                'attributes' => infoAttributeResource::collection($this->attributes),
                'ProductsFromSameCategory' =>ProductResource::collection($proFromSameCat),
            ];
        }



        return [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'price' => (float) $this->price,
            'details' => $this->details,
            'viewed'  => $this->viewed,
            'category' => new CategoryResource(Categories::findOrFail($this->category_id)),
            'media' => AttachmentResource::collection($this->whenLoaded('attachments')),
            //'image' => new AttachmentResource ($this->whenLoaded('firstAttachments')),
            'sale' => new SaleResource($this->whenLoaded('sales')),
            'attributes' => infoAttributeResource::collection($this->attributes),
        ];
    }
}
