<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class ProductCollectionResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
//        dd(
//            $this,
//            ProductResource::collection($this),
//        );
        return [
            'current_page' => $this->currentPage(),
//            'data' => $this->collection,
//            'data' => ProductCollectionResource::collection($this),
            'data' => ProductResource::collection($this),
//            'links' => [
//                'self' => 'link-value',
//            ],

            'first_page_url' => $this->url(1),
            'from' => 1,
            'last_page' => $this->lastPage(),
            'last_page_url' => $this->url($this->lastPage()),
            'links' => [
                $this->getUrlRange(1, $this->lastPage()),
                $this->links(),
//                [
////                    "url" => $this->url(),
//                    "label" => "&laquo; Previous",
//                    "active" => false,
//                ],
            ],
            'next_page_url' => $this->nextPageUrl(),
            'path' => $this->path(),
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->count(),
            'total' => $this->total(),
        ];
    }
}
