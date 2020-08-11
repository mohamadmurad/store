<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'id' => (int)$this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'logo' => $request->getSchemeAndHttpHost() . '/'. config('app.COMPANY_LOGO_PATH','files/companyLogos/') .$this->logo,
        ];
    }
}
