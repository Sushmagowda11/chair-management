<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request->validated());
            return new LoginResource($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function me()
{
    $user = auth()->user()->load('userType');

    return response()->json([
        'data' => [
            'id' => $user->id,
            'email' => $user->email,
            'role' => optional($user->userType)->name,
        ]
    ]);
}


public function logout(Request $request)
{
    $request->user()->token()->revoke();

    return response()->json([
        'message' => 'Logged out successfully'
    ]);
}

}
