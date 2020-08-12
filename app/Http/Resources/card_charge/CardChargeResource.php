<?php

namespace App\Http\Resources\card_charge;

use App\Http\Resources\Card\CardResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CardChargeResource extends JsonResource
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
            'amount' => (int) $this->amount,
            'cost' => (float) $this->cost,
            'chargeDate'=> $this->chargeDate,
            'admin' => new UserResource($this->whenLoaded('admin')),
            'card' => new CardResource($this->whenLoaded('card')),
        ];
    }
}