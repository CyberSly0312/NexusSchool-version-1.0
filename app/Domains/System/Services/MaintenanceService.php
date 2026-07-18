<?php

declare(strict_types=1);

namespace App\Domains\System\Services;

use App\Domains\System\DTOs\MaintenanceWindowData;
use App\Domains\System\Events\MaintenanceEnded;
use App\Domains\System\Events\MaintenanceStarted;
use App\Domains\System\Rules\MaintenanceWindowRules;
use App\Models\MaintenanceWindow;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class MaintenanceService
{
    public function __construct(
        protected MaintenanceWindow $model = new MaintenanceWindow(),
    ) {
    }

    public function create(array $data): MaintenanceWindowData
    {
        $validated = MaintenanceWindowRules::validate($data, 'create');

        $overlapping = $this->model
            ->where('status', '!=', 'completed')
            ->where('starts_at', '<', $validated['ends_at'])
            ->where('ends_at', '>', $validated['starts_at'])
            ->exists();

        if ($overlapping) {
            throw ValidationException::withMessages([
                'starts_at' => 'This maintenance window overlaps with an existing one.',
            ]);
        }

        $window = $this->model->create($validated);

        return MaintenanceWindowData::create($window->toArray());
    }

    public function update(MaintenanceWindow $window, array $data): MaintenanceWindowData
    {
        if ($window->status === 'active') {
            throw ValidationException::withMessages([
                'status' => 'Cannot update an active maintenance window.',
            ]);
        }

        $validated = MaintenanceWindowRules::validate($data, 'update', $window->id);

        $window->update($validated);

        return MaintenanceWindowData::create($window->fresh()->toArray());
    }

    public function start(MaintenanceWindow $window): MaintenanceWindowData
    {
        if ($window->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => 'Only pending windows can be started.',
            ]);
        }

        $window->update([
            'status' => 'active',
            'actual_start_at' => now(),
        ]);

        MaintenanceStarted::dispatch($window);

        return MaintenanceWindowData::create($window->fresh()->toArray());
    }

    public function end(MaintenanceWindow $window): MaintenanceWindowData
    {
        if ($window->status !== 'active') {
            throw ValidationException::withMessages([
                'status' => 'Only active windows can be ended.',
            ]);
        }

        $window->update([
            'status' => 'completed',
            'actual_end_at' => now(),
        ]);

        MaintenanceEnded::dispatch($window);

        return MaintenanceWindowData::create($window->fresh()->toArray());
    }

    public function delete(MaintenanceWindow $window): void
    {
        if ($window->status === 'active') {
            throw ValidationException::withMessages([
                'status' => 'Cannot delete an active maintenance window. End it first.',
            ]);
        }

        $window->delete();
    }

    public function isActive(?string $schoolId = null): bool
    {
        $query = $this->model->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());

        if ($schoolId) {
            $query->where(function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)->orWhereNull('school_id');
            });
        }

        return $query->exists();
    }

    public function isReadonly(?string $schoolId = null): bool
    {
        return $this->model->where('status', 'active')
            ->where('is_readonly', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->exists();
    }

    public function findById(string $id): MaintenanceWindow
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('starts_at', 'desc')->paginate($perPage);
    }
}
