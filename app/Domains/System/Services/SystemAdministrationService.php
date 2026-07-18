<?php

declare(strict_types=1);

namespace App\Domains\System\Services;

use App\Domains\System\DTOs\SystemSettingData;
use App\Domains\System\Events\SystemSettingUpdated;
use App\Domains\System\Rules\SystemSettingRules;
use App\Models\SystemSetting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class SystemAdministrationService
{
    public function __construct(
        protected SystemSetting $model = new SystemSetting(),
    ) {
    }

    public function create(array $data): SystemSettingData
    {
        $validated = SystemSettingRules::validate($data, 'create');

        $existing = $this->model->where('school_id', $validated['school_id'] ?? null)
            ->where('group', $validated['group'])
            ->where('key', $validated['key'])
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'key' => 'A setting with this group and key already exists.',
            ]);
        }

        if (($validated['type'] ?? 'string') === 'encrypted' && isset($validated['value'])) {
            $validated['value'] = Crypt::encryptString($validated['value']);
            $validated['is_encrypted'] = true;
        }

        $setting = $this->model->create($validated);

        $this->clearSettingCache($setting);

        return SystemSettingData::create($setting->toArray());
    }

    public function update(SystemSetting $setting, array $data): SystemSettingData
    {
        if ($setting->is_readonly) {
            throw ValidationException::withMessages([
                'value' => 'This setting is read-only and cannot be modified.',
            ]);
        }

        $validated = SystemSettingRules::validate($data, 'update', $setting->id);

        if (isset($validated['value']) && $setting->type === 'encrypted') {
            $validated['value'] = Crypt::encryptString($validated['value']);
            $validated['is_encrypted'] = true;
        }

        $oldValues = $setting->toArray();
        $setting->update($validated);

        SystemSettingUpdated::dispatch($setting->fresh(), $oldValues);

        $this->clearSettingCache($setting);

        return SystemSettingData::create($setting->fresh()->toArray());
    }

    public function get(string $group, string $key, ?string $schoolId = null): ?string
    {
        $cacheKey = "system_setting:{$schoolId}:{$group}:{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($group, $key, $schoolId) {
            $setting = $this->model
                ->where('group', $group)
                ->where('key', $key)
                ->where('school_id', $schoolId)
                ->first();

            if (! $setting) {
                $setting = $this->model
                    ->where('group', $group)
                    ->where('key', $key)
                    ->whereNull('school_id')
                    ->first();
            }

            return $setting?->getDecryptedValue();
        });
    }

    public function set(string $group, string $key, ?string $value, ?string $schoolId = null): SystemSettingData
    {
        $setting = $this->model
            ->where('group', $group)
            ->where('key', $key)
            ->where('school_id', $schoolId)
            ->first();

        if (! $setting) {
            return $this->create([
                'school_id' => $schoolId,
                'group' => $group,
                'key' => $key,
                'value' => $value,
            ]);
        }

        return $this->update($setting, ['value' => $value]);
    }

    public function getGroup(string $group, ?string $schoolId = null): array
    {
        $query = $this->model->where('group', $group);

        if ($schoolId) {
            $query->where(function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)
                    ->orWhereNull('school_id');
            });
        } else {
            $query->whereNull('school_id');
        }

        return $query->get()->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->getDecryptedValue()];
        })->toArray();
    }

    public function delete(SystemSetting $setting): void
    {
        if ($setting->is_system) {
            throw ValidationException::withMessages([
                'setting' => 'System settings cannot be deleted.',
            ]);
        }

        $this->clearSettingCache($setting);
        $setting->delete();
    }

    public function findById(string $id): SystemSetting
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['group'])) {
            $query->where('group', $filters['group']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('group')->orderBy('key')->paginate($perPage);
    }

    private function clearSettingCache(SystemSetting $setting): void
    {
        Cache::forget("system_setting:{$setting->school_id}:{$setting->group}:{$setting->key}");
        Cache::forget("system_setting::{$setting->group}:{$setting->key}");
    }
}
