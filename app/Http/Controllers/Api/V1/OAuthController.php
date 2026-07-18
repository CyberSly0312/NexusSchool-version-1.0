<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Api\Services\OAuthService;
use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OAuthController extends Controller
{
    public function __construct(
        protected OAuthService $service,
    ) {
    }

    public function token(Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $client = ApiClient::where('slug', $request->client_id)
            ->orWhere('id', $request->client_id)
            ->first();

        if (! $client || ! hash_equals((string) $client->secret, (string) $request->client_secret)) {
            throw ValidationException::withMessages(['client' => ['Invalid client credentials.']]);
        }

        if (! $client->is_active) {
            throw ValidationException::withMessages(['client' => ['API client is disabled.']]);
        }

        $tokenData = $this->service->issueToken(
            $client,
            $request->email,
            $request->password,
            $request->input('scopes', ['read']),
        );

        return response()->json([
            'token' => $tokenData->token,
            'expires_at' => $tokenData->expiresAt,
            'token_type' => 'Bearer',
        ]);
    }

    public function revoke(Request $request): JsonResponse
    {
        $token = $request->input('api_token');
        $this->service->revokeToken($token);

        return response()->json(['message' => 'Token revoked.']);
    }

    public function me(Request $request): JsonResponse
    {
        $token = $request->input('api_token');
        $user = $request->input('api_user');

        return response()->json([
            'user' => [
                'id' => $user?->id,
                'email' => $user?->email,
                'name' => $user?->full_name ?? null,
            ],
            'client' => [
                'id' => $token->apiClient->id,
                'name' => $token->apiClient->name,
            ],
            'abilities' => $token->abilities,
        ]);
    }
}
