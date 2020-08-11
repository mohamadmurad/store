<?php

namespace App\Http\Resources\Company;

use App\Http\Resources\User\SampleUserResource;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyBranchResource extends JsonResource
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
            'phone' => $this->phone,
            'company' => (int) $this->company_id,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'user' => new SampleUserResource(User::findOrFail($this->user_id)),

        ];
    }
}
