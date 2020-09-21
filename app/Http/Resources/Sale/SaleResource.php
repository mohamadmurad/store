<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'sale' => $this->saleRate,
            'price' => $this->newPrice,
            'start' => $this->start,
            'end' => $this->end,
        ];
    }
}
