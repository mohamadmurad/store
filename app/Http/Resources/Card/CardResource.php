<?php

namespace App\Http\Resources\Card;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
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
           'code' => $this->code,
            'pin' => (int) $this->pin,
            'balance' => (float) $this->balance,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
