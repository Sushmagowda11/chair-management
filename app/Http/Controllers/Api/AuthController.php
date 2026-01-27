<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Models\Version;
use App\Models\Dotenv;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    // LOGIN
    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request->validated());

            if (!empty($data['user']->password_plain)) {
                $data['user']->update([
                    'password_plain' => null
                ]);
            }

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
        $dotenv = Dotenv::select('hashKey')->first();

        return response()->json([
            'code'    => 200,
            'hashKey' => $dotenv?->hashKey,
        ]);
    }
}
