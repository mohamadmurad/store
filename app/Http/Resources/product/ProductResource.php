<?php

namespace App\Http\Resources\product;

use App\Categories;
use App\Http\Resources\Attachment\AttachmentResource;
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

        //dd($this->whenLoaded('sales'));
        return [
            'identifier' => $this->id,
            'title' => $this->name,
            'latinTitle' => $this->latinName,
            'productCode' => $this->code,
            'stock' => $this->quantity,
            'situation' => $this->status,
            'price' => (float) $this->price,
            'description' => $this->details,
            'parent' => $this->parent_id,
            'category' => new CategoryResource(Categories::findOrFail($this->category_id)),
            'media' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'sale' => SaleResource::collection($this->whenLoaded('sales')),
        ];
    }
}
