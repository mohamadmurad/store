<?php

namespace App\Http\Resources\Deposit;

use App\Http\Resources\Card\CardResource;
use App\Http\Resources\User\UserResource;
use http\Env\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class DepositResource extends JsonResource
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
            'depositDate'=> $this->depositDate->format('Y.m.d H:i:s'),
            'admin' => new UserResource($this->whenLoaded('admin')),
            'card' => new CardResource($this->whenLoaded('card')),
        ];
    }


}
