<?php

namespace App\Services;

use App\Models\Version;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthService
{
    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        if ($user->status != 1) {
            throw new \Exception('Account is inactive');
        }

        $version = Version::select('version_panel')->first();

        $token = $user->createToken('api-token')->plainTextToken;

        $expiresAt = config('sanctum.expiration')
            ? Carbon::now()->addMinutes(config('sanctum.expiration'))
            : null;

        return [
            'token'      => $token,
            'user'       => $user,
            'version'    => $version,
            'expires_at' => $expiresAt?->toDateTimeString(),
        ];
    }
}
