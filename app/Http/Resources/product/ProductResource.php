<?php

namespace App\Http\Resources\product;

use App\Attributes;
use App\Categories;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Http\Resources\Attribute\AttributeResource;
use App\Http\Resources\Attribute\infoAttributeResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Sale\SaleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {


        return [
            'id' => $this->id,
            'name' => $this->name,
            'latinName' => $this->latinName,
            'code' => $this->code,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'price' => (float) $this->price,
            'details' => $this->details,
            'viewed'  => $this->viewed,
            'parent_id' => $this->parent_id,
            'category' => new CategoryResource(Categories::findOrFail($this->category_id)),
            'media' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'sale' => new SaleResource($this->whenLoaded('sales')),
            'attributes' => infoAttributeResource::collection($this->attributes),
        ];
    }
}
