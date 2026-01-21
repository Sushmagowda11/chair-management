<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'token' => $this['token'],
            'user' => [
                'id' => $this['user']->id,
                'email' => $this['user']->email,
                'role' => $this['user']->role ?? null,
            ],
        ];
    }
}
