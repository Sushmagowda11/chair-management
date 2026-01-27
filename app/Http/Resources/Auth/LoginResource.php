<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
<<<<<<< HEAD
    public function toArray($request): array
    {
        return [
            'access_token' => $this['token'],   // ✅ REAL TOKEN
            'token_type'   => 'Bearer',
            'user' => [
                'id'    => $this['user']->id,
                'email' => $this['user']->email,
                'role'  => $this['user']->role ?? null,
=======
    public function toArray($request)
    {
        return [
            'token' => $this['token'],
            'user' => [
                'id' => $this['user']->id,
                'email' => $this['user']->email,
                'role' => $this['user']->role ?? null,
>>>>>>> 12d698d386402a5adf1bdb0eee155e55a1882bba
            ],
        ];
    }
}
