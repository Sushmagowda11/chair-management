<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'access_token' => $this['token'],   // ✅ REAL TOKEN
            'token_type'   => 'Bearer',
            'user' => [
                'id'    => $this['user']->id,
                'email' => $this['user']->email,
                'role'  => $this['user']->role ?? null,
            ],
        ];
    }
}
