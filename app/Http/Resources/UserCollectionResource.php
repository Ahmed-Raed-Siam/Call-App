<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollectionResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'current_page' => $this->currentPage(),
            'data' => UserResource::collection($this),
//            'links' => [
//                'self' => 'link-value',
//            ],
            'first_page_url' => $this->url(1),
            'from' => 1,
            'last_page' => $this->lastPage(),
            'last_page_url' => $this->url($this->lastPage()),
            'links' => [
                $this->getUrlRange(1, $this->lastPage()),
//                $this->links(),
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
