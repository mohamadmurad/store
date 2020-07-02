<?php

namespace App\Http\Resources\Attribute;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeBranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $isRelation = false;
        if (count($this->branches)){
            $isRelation = true;
        }
        return [

            'id' => $this->id,
            'name' => $this->name,
            'isRelation' => (boolean)$isRelation,
        ];
    }
}
