<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ServiceResource extends JsonResource
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

//        dd(
//            $this,
//            $service_types = $this->service_types,
//            $service_types->count(),
//        );

        $service_types = $this->service_types;
        if (empty($service_types) || $service_types->count() === 0):
            /*As Ali Shaheen wish */
//            $count_service_types = 'no service type to this service';
            $count_service_types = 0;
        else:
            $count_service_types = $service_types->count();
        endif;

        return [
            'id' => $this->id,
            'name' => $this->name,
            /*As Ali Shaheen wish */
            '_self' => route('api.auth.services.show', $this),
//            '_links' => [
//                '_self' => route('api.auth.services.show', $this),
//            ],
            'description' => $this->description,
//            'icon_path' => $this->icon_path,
            'icon_url' => $this->icon_url,
            'order' => $this->order,
            'count_service_types' => $count_service_types,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ];
    }
}
