<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domains\Api\Services\OAuthService;
use App\Models\ApiClient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json(['message' => 'Authentication token required.'], 401);
        }

        $oauthService = app(OAuthService::class);
        $apiToken = $oauthService->validateToken($token);

        if (! $apiToken) {
            return response()->json(['message' => 'Invalid or expired token.'], 401);
        }

        $client = ApiClient::find($apiToken->api_client_id);
        if (! $client || ! $client->is_active) {
            return response()->json(['message' => 'API client is disabled.'], 403);
        }

        $request->merge([
            'api_token' => $apiToken,
            'api_client' => $client,
            'api_user' => $apiToken->user,
        ]);

        $response = $next($request);

        if (isset($apiToken->abilities) && is_array($apiToken->abilities)) {
            $response->headers->set('X-Token-Abilities', implode(',', $apiToken->abilities));
        }

        return $response;
    }
}
