<?php

namespace App\Domains\Department\Services;

use App\Domains\Department\DTOs\DepartmentData;
use App\Domains\Department\Enums\DepartmentStatus;
use App\Domains\Department\Events\DepartmentArchived;
use App\Domains\Department\Events\DepartmentCreated;
use App\Domains\Department\Events\DepartmentUpdated;
use App\Domains\Department\Rules\DepartmentRules;
use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DepartmentService
{
    public function __construct(
        protected Department $model = new Department,
    ) {}

    public function create(DepartmentData $data): Department
    {
        $validated = DepartmentRules::validate($data->toArray(), 'create');

        $this->validateUniquePerSchool($validated['school_id'], $validated['name'], $validated['code']);

        return DB::transaction(function () use ($validated) {
            $department = $this->model->create($validated);

            DepartmentCreated::dispatch($department);

            return $department;
        });
    }

    public function update(Department $department, array $data): Department
    {
        $validated = DepartmentRules::validate($data, 'update', $department->id);

        $schoolId = $validated['school_id'] ?? $department->school_id;
        $name = $validated['name'] ?? $department->name;
        $code = $validated['code'] ?? $department->code;

        $this->validateUniquePerSchool($schoolId, $name, $code, $department->id);

        $department->update($validated);

        DepartmentUpdated::dispatch($department->refresh());

        return $department->refresh();
    }

    public function delete(Department $department): bool
    {
        $department->delete();

        return true;
    }

    public function archive(Department $department): Department
    {
        $oldStatus = $department->status;

        $department->update(['status' => DepartmentStatus::Archived]);

        DepartmentArchived::dispatch($department->fresh());

        return $department->refresh();
    }

    public function restore(Department $department): Department
    {
        $department->update(['status' => DepartmentStatus::Active]);

        DepartmentUpdated::dispatch($department->refresh());

        return $department->refresh();
    }

    public function findById(int $id): Department
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        return $query->orderBy('order')->orderBy('name')->paginate($perPage);
    }

    private function validateUniquePerSchool(string $schoolId, string $name, string $code, ?int $excludeId = null): void
    {
        $nameQuery = $this->model
            ->where('school_id', $schoolId)
            ->where('name', $name);

        $codeQuery = $this->model
            ->where('school_id', $schoolId)
            ->where('code', $code);

        if ($excludeId) {
            $nameQuery->where('id', '!=', $excludeId);
            $codeQuery->where('id', '!=', $excludeId);
        }

        if ($nameQuery->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A department with this name already exists in this school.',
            ]);
        }

        if ($codeQuery->exists()) {
            throw ValidationException::withMessages([
                'code' => 'A department with this code already exists in this school.',
            ]);
        }
    }
}
