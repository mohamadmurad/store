<?php

namespace App\Http\Resources\product;

use App\Http\Resources\Attachment\AttachmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'identifier' => $this->id,
            'title' => $this->name,
            'LatinTitle' => $this->latinName,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'price' => (float) $this->price,
            'details' => $this->details,
            'media' => AttachmentResource::collection($this->whenLoaded('attachments')),

        ];
    }
}
