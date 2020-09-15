<?php

namespace App\Http\Resources\Branch;

use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchReceivablesResource extends JsonResource
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
            'company' => new CompanyResource($this->whenLoaded('company')) /*|| (int) $this->company_id*/ ,
            'phone' => $this->phone,
            'user' => new UserResource($this->whenLoaded('employee')),
            'balance' => $this->balance,
        ];
    }
}
