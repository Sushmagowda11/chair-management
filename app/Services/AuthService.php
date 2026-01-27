<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        // ✅ STATUS CHECK (BLOCK INACTIVE USERS)
        if ($user->status != 1) {
            throw new \Exception('Account is inactive');
        }

        // ✅ LOAD ROLE RELATION
        $user->load('userType');

        // ✅ SANCTUM TOKEN (CORRECT)
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'token' => $token,
            'user'  => $user,
        ];
    }
}
