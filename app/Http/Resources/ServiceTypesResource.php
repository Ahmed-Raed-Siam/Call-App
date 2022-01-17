<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ServiceTypesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
//        if (empty($this->icon_path)):
//            $icon_url = 'no icon exists';
//        else:
//            $icon_url = url('/uploads/' . $this->icon_path);
//        endif;

        $service = $this->service;

//        dd(
//            $service,
//            empty($service) === false,
//            $service->count() > 0,
//        );

        if (empty($service) === false && $service->count() > 0):
            $service = ServiceResource::make($service);
        else:
            /*As Ali Shaheen wish */
            $service = null;
//            $service = 'no service to this type of service';
        endif;

        $products = $this->products;

//        dd(
//            $service,
//            count($products),
//            empty($products),
//            $products,
//            empty($products) === false,
//            count($products) > 0,
//        );

        $count_products = 0;
        if (empty($products) === false || $products->count() === 0):
            $count_products = $products->count();
            $products = ProductResource::make($products);
        else:
            /*As Ali Shaheen wish */
            $products = null;
//        $products = 'no products to this type of service';

        endif;

//        dd(
//            $service,
//            $products,
//        );

        return [
            'id' => $this->id,
            'name' => $this->name,
            /*As Ali Shaheen wish */
            '_self' => route('api.auth.services.types.show', $this),
//            '_links' => [
//                '_self' => route('api.auth.services.types.show', $this),
//            ],
            'description' => $this->description,
//            'icon_path' => $this->icon_path,
            'icon_url' => $this->icon_url,
            'order' => $this->order,
            'service_id' => $this->service_id,
            'service' => $service,
//            'service' => ServiceResource::make($service),
//            'products' => $products,
            'count_products' => $count_products,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ];
    }
}
