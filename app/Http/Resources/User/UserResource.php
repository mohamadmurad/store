<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Card\CardResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'username' => $this->username,
            'location' => $this->location,
            'card' => new CardResource($this->whenLoaded('card')),
        ];
    }
}
