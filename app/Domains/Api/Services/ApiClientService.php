<?php

declare(strict_types=1);

namespace App\Domains\Api\Services;

use App\Domains\Api\DTOs\ApiClientData;
use App\Domains\Api\Events\ApiClientDisabled;
use App\Domains\Api\Rules\ApiClientRules;
use App\Models\ApiClient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ApiClientService
{
    public function __construct(
        protected ApiClient $model = new ApiClient(),
    ) {
    }

    public function create(array $data): ApiClientData
    {
        $validated = ApiClientRules::validate($data, 'create');
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['secret'] = Str::random(64);
        $this->validateUniqueSlugPerSchool($validated['school_id'], $validated['name'], null);

        return DB::transaction(function () use ($validated) {
            $client = $this->model->create($validated);

            return ApiClientData::create($client->toArray());
        });
    }

    public function update(ApiClient $client, array $data): ApiClientData
    {
        $validated = ApiClientRules::validate($data, 'update', $client->id);

        if (isset($validated['name'])) {
            $this->validateUniqueSlugPerSchool($client->school_id, $validated['name'], $client->id);
        }

        $client->update($validated);

        return ApiClientData::create($client->fresh()->toArray());
    }

    public function disable(ApiClient $client): ApiClientData
    {
        $client->update(['is_active' => false]);
        ApiClientDisabled::dispatch($client->fresh());

        return ApiClientData::create($client->fresh()->toArray());
    }

    public function enable(ApiClient $client): ApiClientData
    {
        $client->update(['is_active' => true]);

        return ApiClientData::create($client->fresh()->toArray());
    }

    public function delete(ApiClient $client): void
    {
        $client->delete();
    }

    public function findBySlug(string $schoolId, string $slug): ApiClient
    {
        return $this->model->where('school_id', $schoolId)->where('slug', $slug)->firstOrFail();
    }

    public function findById(string $id): ApiClient
    {
        return $this->model->with(['tokens', 'webhooks'])->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function regenerateSecret(ApiClient $client): ApiClientData
    {
        $client->update(['secret' => Str::random(64)]);

        return ApiClientData::create($client->fresh()->toArray());
    }

    private function validateUniqueSlugPerSchool(string|int $schoolId, string $name, ?string $excludeId): void
    {
        $slug = Str::slug($name);
        $query = $this->model->where('school_id', $schoolId)->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages(['name' => 'An API client with this name already exists in this school.']);
        }
    }
}
