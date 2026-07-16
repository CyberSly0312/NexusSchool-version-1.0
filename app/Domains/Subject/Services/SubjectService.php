<?php

namespace App\Domains\Subject\Services;

use App\Domains\Subject\DTOs\SubjectData;
use App\Domains\Subject\Enums\SubjectStatus;
use App\Domains\Subject\Events\SubjectActivated;
use App\Domains\Subject\Events\SubjectArchived;
use App\Domains\Subject\Events\SubjectCreated;
use App\Domains\Subject\Events\SubjectUpdated;
use App\Domains\Subject\Rules\SubjectRules;
use App\Models\Subject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubjectService
{
    public function __construct(
        protected Subject $model = new Subject,
    ) {}

    public function create(SubjectData $data): Subject
    {
        $validated = SubjectRules::validate($data->toArray(), 'create');

        $this->validateUniqueNamePerSchool($validated['school_id'], $validated['name']);
        $this->validateUniqueCodePerSchool($validated['school_id'], $validated['code']);
        $this->validateCoreElectiveConflict($validated);
        $this->validateUniqueDisplayOrder($validated['display_order']);

        return DB::transaction(function () use ($validated) {
            $subject = $this->model->create($validated);

            SubjectCreated::dispatch($subject);

            return $subject;
        });
    }

    public function update(Subject $subject, array $data): Subject
    {
        $validated = SubjectRules::validate($data, 'update', $subject->id);

        $schoolId = $validated['school_id'] ?? $subject->school_id;
        $name = $validated['name'] ?? $subject->name;
        $code = $validated['code'] ?? $subject->code;

        $this->validateUniqueNamePerSchool($schoolId, $name, $subject->id);
        $this->validateUniqueCodePerSchool($schoolId, $code, $subject->id);

        $merged = array_merge([
            'is_elective' => $subject->is_elective,
            'is_core_subject' => $subject->is_core_subject,
        ], $validated);
        $this->validateCoreElectiveConflict($merged);

        if (isset($validated['display_order'])) {
            $this->validateUniqueDisplayOrder($validated['display_order'], $subject->id);
        }

        $subject->update($validated);

        SubjectUpdated::dispatch($subject->refresh());

        return $subject->refresh();
    }

    public function delete(Subject $subject): bool
    {
        $subject->delete();

        return true;
    }

    public function archive(Subject $subject): Subject
    {
        $subject->update(['status' => SubjectStatus::Archived]);

        SubjectArchived::dispatch($subject->fresh());

        return $subject->refresh();
    }

    public function activate(Subject $subject): Subject
    {
        $subject->update(['status' => SubjectStatus::Active]);

        SubjectActivated::dispatch($subject->fresh());

        return $subject->refresh();
    }

    public function findById(int $id): Subject
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
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['subject_category_id'])) {
            $query->where('subject_category_id', $filters['subject_category_id']);
        }

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

        if (isset($filters['is_core_subject'])) {
            $query->where('is_core_subject', $filters['is_core_subject']);
        }

        if (isset($filters['is_elective'])) {
            $query->where('is_elective', $filters['is_elective']);
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
                'name' => 'A subject with this name already exists in this school.',
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
                'code' => 'A subject with this code already exists in this school.',
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

    private function validateCoreElectiveConflict(array $data): void
    {
        $isElective = $data['is_elective'] ?? false;
        $isCore = $data['is_core_subject'] ?? false;

        if ($isElective && $isCore) {
            throw ValidationException::withMessages([
                'is_elective' => 'A subject cannot be both core and elective at the same time.',
            ]);
        }
    }
}
