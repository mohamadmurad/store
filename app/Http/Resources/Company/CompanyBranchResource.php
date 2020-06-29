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
            'balance' => (float)$this->balance,
            'company' => (int) $this->company_id,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'user' => new SampleUserResource(User::findOrFail($this->user_id)),
            //'posts' => Product::collection($this->whenLoaded('products')),
           // 'category' => new CategoryResource(Categories::findOrFail($this->category_id)),
        ];
    }
}
