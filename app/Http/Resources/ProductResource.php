<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $service_type = $this->service_type;

//        dd(
//            $this,
//            $this->service_type,
//            $service_type = $this->service_type()->get(),
//        );

        return [
            'id' => $this->id,
            'name' => $this->name,
            /*As Ali Shaheen wish */
            '_self' => route('api.auth.products.show', $this),
//            '_links' => [
//                '_self' => route('api.auth.products.show', $this),
//            ],
            'description' => $this->description,
//            'image' => $this->image,
            'image_url' => $this->image_url,
            'order' => $this->order,
            'cost' => $this->cost,
            'service_type_id' => $service_type->id,
//            'service_type' => $service_type,
            'service_type' => ServiceTypesResource::make($service_type),
//            'service_type_url' => route('api.services.types.show', $service_type),
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ];
    }
}
