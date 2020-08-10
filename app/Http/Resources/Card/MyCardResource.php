<?php

namespace App\Http\Resources\Card;

use Illuminate\Http\Resources\Json\JsonResource;

class MyCardResource extends JsonResource
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
            'code'=> $this->code,
            'balance' => (float) $this->balance,
        ];
    }
}
