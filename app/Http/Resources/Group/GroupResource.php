<?php

namespace App\Http\Resources\Group;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'secret' => $this->when($this->name === 'et', 'secret-value'),
            //'posts' => Product::collection($this->whenLoaded('products')),

        ];
    }

    public function with($request)
    {
        return [
            'meta' => [
                'sds' => 'dfd',
            ],

        ];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->header('X-fffff', 'True');
    }
}
