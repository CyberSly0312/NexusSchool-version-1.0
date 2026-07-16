<?php

namespace App\Domains\SubjectCategory\Services;

use App\Domains\SubjectCategory\DTOs\SubjectCategoryData;
use App\Domains\SubjectCategory\Enums\SubjectCategoryStatus;
use App\Domains\SubjectCategory\Events\SubjectCategoryArchived;
use App\Domains\SubjectCategory\Events\SubjectCategoryCreated;
use App\Domains\SubjectCategory\Events\SubjectCategoryUpdated;
use App\Domains\SubjectCategory\Rules\SubjectCategoryRules;
use App\Models\SubjectCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubjectCategoryService
{
    public function __construct(
        protected SubjectCategory $model = new SubjectCategory,
    ) {}

    public function create(SubjectCategoryData $data): SubjectCategory
    {
        $validated = SubjectCategoryRules::validate($data->toArray(), 'create');

        $this->validateUniqueNamePerSchool($validated['school_id'], $validated['name']);
        $this->validateUniqueCodePerSchool($validated['school_id'], $validated['code']);
        $this->validateUniqueDisplayOrder($validated['display_order']);

        return DB::transaction(function () use ($validated) {
            $subjectCategory = $this->model->create($validated);

            SubjectCategoryCreated::dispatch($subjectCategory);

            return $subjectCategory;
        });
    }

    public function update(SubjectCategory $subjectCategory, array $data): SubjectCategory
    {
        $validated = SubjectCategoryRules::validate($data, 'update', $subjectCategory->id);

        $schoolId = $validated['school_id'] ?? $subjectCategory->school_id;
        $name = $validated['name'] ?? $subjectCategory->name;
        $code = $validated['code'] ?? $subjectCategory->code;

        $this->validateUniqueNamePerSchool($schoolId, $name, $subjectCategory->id);
        $this->validateUniqueCodePerSchool($schoolId, $code, $subjectCategory->id);

        if (isset($validated['display_order'])) {
            $this->validateUniqueDisplayOrder($validated['display_order'], $subjectCategory->id);
        }

        $subjectCategory->update($validated);

        SubjectCategoryUpdated::dispatch($subjectCategory->refresh());

        return $subjectCategory->refresh();
    }

    public function delete(SubjectCategory $subjectCategory): bool
    {
        $subjectCategory->delete();

        return true;
    }

    public function archive(SubjectCategory $subjectCategory): SubjectCategory
    {
        $subjectCategory->update(['status' => SubjectCategoryStatus::Archived]);

        SubjectCategoryArchived::dispatch($subjectCategory->fresh());

        return $subjectCategory->refresh();
    }

    public function activate(SubjectCategory $subjectCategory): SubjectCategory
    {
        $subjectCategory->update(['status' => SubjectCategoryStatus::Active]);

        SubjectCategoryUpdated::dispatch($subjectCategory->fresh());

        return $subjectCategory->refresh();
    }

    public function findById(int $id): SubjectCategory
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

        return $query->orderBy('display_order')->orderBy('name')->paginate($perPage);
    }

    private function validateUniqueNamePerSchool(string $schoolId, string $name, ?int $excludeId = null): void
    {
        $query = $this->model
            ->where('school_id', $schoolId)
            ->where('name', $name);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A subject category with this name already exists in this school.',
            ]);
        }
    }

    private function validateUniqueCodePerSchool(string $schoolId, string $code, ?int $excludeId = null): void
    {
        $query = $this->model
            ->where('school_id', $schoolId)
            ->where('code', $code);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'code' => 'A subject category with this code already exists in this school.',
            ]);
        }
    }

    private function validateUniqueDisplayOrder(int $displayOrder, ?int $excludeId = null): void
    {
        $query = $this->model->where('display_order', $displayOrder);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'display_order' => 'This display order is already taken.',
            ]);
        }
    }
}
