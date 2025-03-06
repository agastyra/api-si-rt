<?php

namespace App\Http\Controllers\Auth;

use App\Enum\TokenAbility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticateController extends Controller
{
    public function store(LoginRequest $request): JsonResponse
    {
        try {
            $request->authenticate();
            $user = Auth::user();

            $accessToken = $user->createToken("Access Token $user->id", [TokenAbility::ACCESS_API->value], now()
                ->addMinutes(config('sanctum.access_token_expiration')))
                ->plainTextToken;
            $refreshToken = $user->createToken("Refresh Token $user->id", [TokenAbility::ISSUE_ACCESS_TOKEN->value], now()
                ->addMinutes(config('sanctum.refresh_token_expiration')))
                ->plainTextToken;

            return response()->json([
                    'message' => 'Successfully Authenticated',
                    'status' => 200,
                    '$accessToken' => $accessToken,
                    '$refreshToken' => $refreshToken,
                    'user' => $user]
                , 200
            );
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Failed to authenticate',
                'status' => 422,
                'errors' => $e->validator->errors(),
            ], 422);
        }
    }

    public function destroy(): JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully.','status' => 200], 200);
    }

    public function refreshToken(): JsonResponse
    {
        $user = Auth::user();
        $accessToken = $user->createToken("Access Token $user->id", [TokenAbility::ACCESS_API->value], now()
            ->addMinutes(config('sanctum.access_token_expiration')))
            ->plainTextToken;

        return response()->json([
            'message' => 'Successfully refreshed token',
            'status' => 200,
            'accessToken' => $accessToken
        ]);
    }
}
