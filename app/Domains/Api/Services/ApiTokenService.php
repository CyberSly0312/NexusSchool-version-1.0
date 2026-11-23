<?php

declare(strict_types=1);

namespace App\Domains\Api\Services;

use App\Domains\Api\DTOs\ApiTokenData;
use App\Domains\Api\Events\ApiTokenCreated;
use App\Domains\Api\Events\ApiTokenRevoked;
use App\Domains\Api\Rules\ApiTokenRules;
use App\Models\ApiClient;
use App\Models\ApiToken;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiTokenService
{
    public function __construct(
        protected ApiToken $model = new ApiToken(),
    ) {
    }

    public function create(ApiClient $client, array $data): ApiTokenData
    {
        $validated = ApiTokenRules::validate($data, 'create');

        if (isset($validated['user_id'])) {
            $user = \App\Models\User::query()
                ->whereKey($validated['user_id'])
                ->where('school_id', $client->school_id)
                ->first();

            if (! $user || ! $user->isActive()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'user_id' => ['Invalid user.'],
                ]);
            }
        }

        $plainTextToken = Str::random(80);
        $validated['token'] = hash('sha256', $plainTextToken);
        $validated['api_client_id'] = $client->id;
        $validated['school_id'] = $client->school_id;

        return DB::transaction(function () use ($validated, $plainTextToken) {
            $token = $this->model->create($validated);
            $result = ApiTokenData::create(array_merge(
                $token->toArray(),
                ['token' => $plainTextToken],
            ));
            ApiTokenCreated::dispatch($token->fresh());

            return $result;
        });
    }

    public function revoke(ApiToken $token): void
    {
        $token->delete();
        ApiTokenRevoked::dispatch($token);
    }

    public function findByToken(string $hashedToken): ?ApiToken
    {
        return $this->model->where('token', $hashedToken)->where('is_active', true)->first();
    }

    public function validateToken(ApiToken $token): bool
    {
        if (! $token->is_active) {
            return false;
        }

        if ($token->isExpired()) {
            return false;
        }

        return true;
    }

    public function paginateByClient(string $clientId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->where('api_client_id', $clientId);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function touchLastUsed(ApiToken $token): void
    {
        $token->update(['last_used_at' => now()]);
    }
}
