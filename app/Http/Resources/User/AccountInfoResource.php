<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Card\CardResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountInfoResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'username' => $this->username,
            'location' => $this->location,
            'card' => new CardResource($this->whenLoaded('card')),
        ];
    }
}
