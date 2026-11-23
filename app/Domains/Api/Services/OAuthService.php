<?php

declare(strict_types=1);

namespace App\Domains\Api\Services;

use App\Domains\Api\DTOs\ApiTokenData;
use App\Domains\School\Models\School;
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
        $this->assertClientTenantContext($client);

        $user = User::query()
            ->where('email', $email)
            ->where('school_id', $client->school_id)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages(['credentials' => ['Invalid credentials.']]);
        }

        if (! $user->isActive()) {
            throw ValidationException::withMessages(['credentials' => ['Account is not active.']]);
        }

        if ((string) $user->school_id !== (string) $client->school_id) {
            throw ValidationException::withMessages(['credentials' => ['Invalid credentials.']]);
        }

        if (! $user->hasActiveSchool()) {
            throw ValidationException::withMessages(['credentials' => ['Invalid credentials.']]);
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

        $client = $token->apiClient;
        $user = $token->user;

        if (! $client instanceof ApiClient || ! $user instanceof User) {
            return null;
        }

        if (! $client->is_active) {
            return null;
        }

        $clientSchoolModel = $client->school;
        if (! $clientSchoolModel instanceof School || ! $clientSchoolModel->isActive()) {
            return null;
        }

        $tokenSchool = (string) $token->school_id;
        $clientSchool = (string) $client->school_id;
        $userSchool = (string) $user->school_id;

        if ($tokenSchool === '' || $clientSchool === '' || $userSchool === '') {
            return null;
        }

        if ($tokenSchool !== $clientSchool || $tokenSchool !== $userSchool) {
            return null;
        }

        if (! $user->isActive()) {
            return null;
        }

        if (! $user->hasActiveSchool()) {
            return null;
        }

        if ((string) $user->school_id !== $tokenSchool) {
            return null;
        }

        if (function_exists('tenancy') && tenancy()->initialized && tenant() !== null) {
            $tenantKey = (string) tenant()->getKey();
            if ($tenantKey !== $tokenSchool) {
                return null;
            }
        }

        $this->tokenService->touchLastUsed($token);

        return $token;
    }

    private function assertClientTenantContext(ApiClient $client): void
    {
        $clientSchool = (string) $client->school_id;

        if ($clientSchool === '') {
            throw ValidationException::withMessages(['client' => ['Invalid client credentials.']]);
        }

        $school = $client->school;
        if (! $school instanceof School || ! $school->isActive()) {
            throw ValidationException::withMessages(['client' => ['Invalid client credentials.']]);
        }

        if (function_exists('tenancy') && tenancy()->initialized && tenant() !== null) {
            $tenantKey = (string) tenant()->getKey();
            if ($tenantKey !== $clientSchool) {
                throw ValidationException::withMessages(['client' => ['Invalid client credentials.']]);
            }
        }
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
