<?php

namespace App\Http\Resources\Category;

use App\Http\Resources\product\ProductResource;
use App\Http\Resources\product\WebProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class HomePageResource extends JsonResource
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
            'parent' => (int)$this->parent_id,
            'products' => WebProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
