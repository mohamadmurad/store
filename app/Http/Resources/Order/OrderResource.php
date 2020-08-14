<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Offer\OfferResource;
use App\Http\Resources\product\WebProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'id'=>$this->id,
            'date' => $this->date,
            'discount' => $this->discount,
            'delevareAmount' => $this->delevareAmount,
            'coupon_id' => $this->coupon_id,
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'products' => WebProductResource::collection($this->whenLoaded('products')),
            'offers' => OfferResource::collection($this->whenLoaded('offers')),
        ];
    }
}
