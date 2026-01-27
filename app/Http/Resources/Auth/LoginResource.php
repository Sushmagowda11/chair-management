<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'access_token' => $this['token'],
            'expires_at'   => $this['expires_at'],
            'token_type'   => 'Bearer',
            'user' => [
                'id'           => $this['user']->id,
                'name'         => $this['user']->name,
                'email'        => $this['user']->email,
                'user_type_id' => $this['user']->user_type_id,
                'role'         => optional($this['user']->userType)->name,
            ],
            'version' => $this['version'],
        ];
    }
}
