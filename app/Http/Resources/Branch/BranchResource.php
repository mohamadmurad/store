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
            'name' => $this->name,
            'location' =>$this->location,
            'balance' => (float)$this->balance,
            'company' => (int) $this->company_id,
            'user' => (int)$this->user_id,
        ];
    }
}
