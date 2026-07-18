<?php

declare(strict_types=1);

namespace App\Domains\Api\Services;

use App\Domains\Api\DTOs\ApiTokenData;
use App\Models\ApiClient;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OAuthService
{
    public function __construct(
        protected ApiTokenService $tokenService,
    ) {
    }

    public function issueToken(ApiClient $client, string $email, string $password, array $scopes = []): ApiTokenData
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages(['credentials' => ['Invalid credentials.']]);
        }

        if (! $user->isActive()) {
            throw ValidationException::withMessages(['credentials' => ['Account is not active.']]);
        }

        return $this->tokenService->create($client, [
            'name' => 'OAuth Token - '.$email,
            'user_id' => $user->id,
            'abilities' => $scopes ?: ['*'],
            'expires_at' => now()->addHours(24)->toDateTimeString(),
        ]);
    }

    public function validateToken(string $plainTextToken): ?ApiToken
    {
        $hashedToken = hash('sha256', $plainTextToken);
        $token = $this->tokenService->findByToken($hashedToken);

        if (! $token || ! $this->tokenService->validateToken($token)) {
            return null;
        }

        $this->tokenService->touchLastUsed($token);

        return $token;
    }

    public function revokeToken(ApiToken $token): void
    {
        $this->tokenService->revoke($token);
    }

    public function revokeAllForClient(ApiClient $client): void
    {
        $client->tokens()->each(fn ($token) => $this->tokenService->revoke($token));
    }
}
