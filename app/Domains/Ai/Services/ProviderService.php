<?php

declare(strict_types=1);

namespace App\Domains\Ai\Services;

use App\Domains\Ai\DTOs\AiProviderData;
use App\Domains\Ai\Enums\AiProviderDriver;
use App\Domains\Ai\Events\ProviderChanged;
use App\Domains\Ai\Rules\AiProviderRules;
use App\Models\AiProvider as AiProviderModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProviderService
{
    public function __construct(
        protected AiProviderModel $model = new AiProviderModel(),
    ) {
    }

    public function create(array $data): AiProviderData
    {
        $validated = AiProviderRules::validate($data, 'create');
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $this->validateUniqueSlugPerSchool($validated['school_id'], $validated['name'], null);

        $providerDriver = AiProviderDriver::tryFrom($validated['driver']);
        $validated['supported_models'] = $validated['supported_models']
            ?? config("ai.providers.{$validated['driver']}.supported_models", []);
        $validated['default_model'] = $validated['default_model']
            ?? config("ai.providers.{$validated['driver']}.default_model");

        return DB::transaction(function () use ($validated) {
            $provider = $this->model->create($validated);

            return AiProviderData::create($provider->toArray());
        });
    }

    public function update(AiProviderModel $provider, array $data): AiProviderData
    {
        $validated = AiProviderRules::validate($data, 'update', $provider->id);
        $previousDriver = $provider->driver;

        if (isset($validated['name'])) {
            $this->validateUniqueSlugPerSchool($provider->school_id, $validated['name'], $provider->id);
        }

        $provider->update($validated);

        if (isset($validated['driver']) && $validated['driver'] !== $previousDriver) {
            ProviderChanged::dispatch($provider->fresh(), $previousDriver);
        }

        return AiProviderData::create($provider->fresh()->toArray());
    }

    public function enable(AiProviderModel $provider): AiProviderData
    {
        $provider->update(['is_active' => true]);

        return AiProviderData::create($provider->fresh()->toArray());
    }

    public function disable(AiProviderModel $provider): AiProviderData
    {
        $provider->update(['is_active' => false]);

        return AiProviderData::create($provider->fresh()->toArray());
    }

    public function delete(AiProviderModel $provider): void
    {
        $provider->delete();
    }

    public function findById(string $id): AiProviderModel
    {
        return $this->model->withCount('conversations')->findOrFail($id);
    }

    public function findActiveForSchool(string $schoolId, ?string $driver = null): ?AiProviderModel
    {
        $query = $this->model->where('school_id', $schoolId)->where('is_active', true);

        if ($driver) {
            $query->where('driver', $driver);
        }

        return $query->first();
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

        if (isset($filters['driver'])) {
            $query->where('driver', $filters['driver']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function getSupportedModels(string $driver): array
    {
        return config("ai.providers.{$driver}.supported_models", []);
    }

    private function validateUniqueSlugPerSchool(string $schoolId, string $name, ?string $excludeId): void
    {
        $slug = Str::slug($name);
        $query = $this->model->where('school_id', $schoolId)->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages(['name' => 'An AI provider with this name already exists in this school.']);
        }
    }
}
