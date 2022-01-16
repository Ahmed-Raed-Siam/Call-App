<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use JsonSerializable;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $self = 'api.user';
        if (Route::is('api.user.profile.profile') || Route::is('api.user.profile.update')):
            $self = 'api.user.profile.profile';
        endif;
        return [
            'id' => $this->id,
            'name' => $this->name,
            /*As Ali Shaheen wish */
            '_self' => route($self),
//            '_links' => [
//                '_self' => route($self),
//            ],
            'email' => $this->email,
            'phone_number' => $this->phone_number,
//            'avatar_url' => $this->avatar_url,
            'user_photo_url' => $this->user_photo_url,
            'email_verified_at' => $this->email_verified_at,
        ];
    }
}
