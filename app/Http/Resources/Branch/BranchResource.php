<?php

namespace App\Http\Resources\Branch;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'id' => (int) $this->id,
            'name' => $this->name,
            'location' =>$this->location,
            'balance' => (float)$this->balance,
            'company' => (int) $this->company_id,
            'phone' => $this->phone,
            'user' => (int)$this->user_id,
        ];
    }
}
