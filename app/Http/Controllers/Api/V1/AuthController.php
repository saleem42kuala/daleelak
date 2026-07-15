<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SocialAuthRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\SocialTokenVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Verify a Google/Apple ID token and issue a Sanctum token.
     */
    public function social(SocialAuthRequest $request, SocialTokenVerifier $verifier): JsonResponse
    {
        $identity = $verifier->verify($request->provider, $request->id_token);

        $user = User::updateOrCreate(
            ['email' => $identity['email']],
            [
                'name' => $identity['name'] ?? $identity['email'],
                'password' => bcrypt(str()->random(40)),
            ],
        );

        $token = $user->createToken($request->provider)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * REMOVE BEFORE PRODUCTION.
     *
     * Dev-only login for testing: finds or creates a user by email and issues a
     * Sanctum token with the same response shape as social(). Local env only.
     */
    public function devLogin(Request $request): JsonResponse
    {
        // REMOVE BEFORE PRODUCTION: never expose account impersonation in prod.
        abort_unless(app()->environment('local'), 403);

        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => 'مستخدم تجريبي',
                'password' => bcrypt(str()->random(40)),
            ],
        );

        $token = $user->createToken('dev-login')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * The currently authenticated user.
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->load('country'));
    }
}
