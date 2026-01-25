<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Models\version;
use App\Models\dotenv;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    // LOGIN
    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request->validated());

            return new LoginResource($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 401);
        }
    }

    // AUTH USER DETAILS
    public function me()
    {
        $user = auth()->user()->load('userType');

        return response()->json([
            'data' => [
                'id'    => $user->id,
                'email' => $user->email,
                'role'  => optional($user->userType)->name,
            ]
        ]);
    }

    // LOGOUT (SANCTUM)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    // PANEL MAINTENANCE STATUS
    public function panelCheckStatus()
    {
        // Later this can come from DB or env
        $status = "live"; // live | maintenance

        $version = Version::select('version_panel')->first();

        if ($status === "live") {
            return response()->json([
                'code'    => 201,
                'success' => 'ok',
                'message' => config('messages.maintenance_success'),
                'version' => $version,
            ]);
        }

        return response()->json([
            'code'    => 403,
            'success' => 'fail',
            'message' => config('messages.maintenance_failed'),
            'version' => $version,
        ], 403);
    }

    // FETCH DOTENV HASH KEY
    public function panelFetchDotenv()
    {
        $dotenv = DotEnv::select('hashKey')->first();

        return response()->json([
            'code'    => 200,
            'hashKey' => $dotenv?->hashKey,
        ]);
    }
}
