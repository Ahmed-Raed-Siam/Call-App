<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use JsonSerializable;

class OrdersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $self = '';
        if (Route::is('api.orders.user.current_orders') || Route::is('api.orders.user.completed_orders') || Route::is('api.user.order.order_details')):
            $self = route('api.user.order.order_details', $this);
        endif;

        return [
            'id' => $this->id,
            'number ' => $this->number,
            /*As Ali Shaheen wish */
            '_self' => $self,
//            '_links' => [
//                '_self' => route($self),
//            ],
            'status' => $this->status,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'billing_address' => $this->billing_address,
            'billing_country' => $this->billing_country,
            'billing_city' => $this->billing_city,
            'billing_neighborhood' => $this->billing_neighborhood,
            'billing_street' => $this->billing_street,
            'billing_building_number' => $this->billing_building_number,
            'booking_date' => $this->booking_date,
            'total_cost' => $this->total_cost,
//            'products' => ProductResource::make($this->products),
            'products' => ProductResource::collection($this->products),
//            'service_type_url' => route('api.services.types.show', $service_type),
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ];
    }
}
