<?php

namespace App\Http\Resources\Offer;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\product\ProductResource;
use App\Http\Resources\product\WebProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'number' => $this->number,
            'price' => $this->price,
            'start' => $this->start,
            'end' => $this->end,
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'products' => WebProductResource::collection($this->whenLoaded('products')),
           // 'qq'=>$this->pivote,

        ];
    }
}
