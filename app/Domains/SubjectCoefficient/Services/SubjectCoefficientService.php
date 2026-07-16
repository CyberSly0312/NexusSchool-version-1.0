<?php

namespace App\Domains\SubjectCoefficient\Services;

use App\Domains\SubjectCoefficient\DTOs\SubjectCoefficientData;
use App\Domains\SubjectCoefficient\Enums\SubjectCoefficientStatus;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientActivated;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientArchived;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientCreated;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientUpdated;
use App\Domains\SubjectCoefficient\Rules\SubjectCoefficientRules;
use App\Models\SubjectCoefficient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubjectCoefficientService
{
    public function __construct(
        protected SubjectCoefficient $model = new SubjectCoefficient,
    ) {}

    public function create(SubjectCoefficientData $data): SubjectCoefficient
    {
        $validated = SubjectCoefficientRules::validate($data->toArray(), 'create');

        $this->validateUniqueActiveCoefficient(
            $validated['school_id'],
            $validated['subject_id'],
            $validated['program_id'],
            $validated['grade_level_id'],
            $validated['academic_year_id']
        );

        $this->validateOverlappingDates(
            $validated['school_id'],
            $validated['subject_id'],
            $validated['program_id'],
            $validated['grade_level_id'],
            $validated['academic_year_id'],
            $validated['effective_from'],
            $validated['effective_to']
        );

        return DB::transaction(function () use ($validated) {
            $subjectCoefficient = $this->model->create($validated);

            SubjectCoefficientCreated::dispatch($subjectCoefficient);

            return $subjectCoefficient;
        });
    }

    public function update(SubjectCoefficient $subjectCoefficient, array $data): SubjectCoefficient
    {
        $validated = SubjectCoefficientRules::validate($data, 'update', $subjectCoefficient->id);

        $schoolId = $validated['school_id'] ?? $subjectCoefficient->school_id;
        $subjectId = $validated['subject_id'] ?? $subjectCoefficient->subject_id;
        $programId = $validated['program_id'] ?? $subjectCoefficient->program_id;
        $gradeLevelId = $validated['grade_level_id'] ?? $subjectCoefficient->grade_level_id;
        $academicYearId = $validated['academic_year_id'] ?? $subjectCoefficient->academic_year_id;

        $this->validateUniqueActiveCoefficient(
            $schoolId,
            $subjectId,
            $programId,
            $gradeLevelId,
            $academicYearId,
            $subjectCoefficient->id
        );

        $effectiveFrom = $validated['effective_from'] ?? $subjectCoefficient->effective_from;
        $effectiveTo = $validated['effective_to'] ?? $subjectCoefficient->effective_to;

        $this->validateOverlappingDates(
            $schoolId,
            $subjectId,
            $programId,
            $gradeLevelId,
            $academicYearId,
            $effectiveFrom,
            $effectiveTo,
            $subjectCoefficient->id
        );

        $subjectCoefficient->update($validated);

        SubjectCoefficientUpdated::dispatch($subjectCoefficient->refresh());

        return $subjectCoefficient->refresh();
    }

    public function delete(SubjectCoefficient $subjectCoefficient): bool
    {
        $subjectCoefficient->delete();

        return true;
    }

    public function archive(SubjectCoefficient $subjectCoefficient): SubjectCoefficient
    {
        $subjectCoefficient->update(['status' => SubjectCoefficientStatus::Archived]);

        SubjectCoefficientArchived::dispatch($subjectCoefficient->fresh());

        return $subjectCoefficient->refresh();
    }

    public function activate(SubjectCoefficient $subjectCoefficient): SubjectCoefficient
    {
        $subjectCoefficient->update(['status' => SubjectCoefficientStatus::Active]);

        SubjectCoefficientActivated::dispatch($subjectCoefficient->fresh());

        return $subjectCoefficient->refresh();
    }

    public function findById(int $id): SubjectCoefficient
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('coefficient', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (isset($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

        if (isset($filters['grade_level_id'])) {
            $query->where('grade_level_id', $filters['grade_level_id']);
        }

        if (isset($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        return $query->orderByDesc('effective_from')->paginate($perPage);
    }

    private function validateUniqueActiveCoefficient(
        string $schoolId,
        int $subjectId,
        int $programId,
        int $gradeLevelId,
        ?int $academicYearId,
        ?int $excludeId = null
    ): void {
        $query = $this->model
            ->where('school_id', $schoolId)
            ->where('subject_id', $subjectId)
            ->where('program_id', $programId)
            ->where('grade_level_id', $gradeLevelId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', SubjectCoefficientStatus::Active);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'subject_id' => 'An active coefficient already exists for this subject, program, grade level and academic year combination.',
            ]);
        }
    }

    private function validateOverlappingDates(
        string $schoolId,
        int $subjectId,
        int $programId,
        int $gradeLevelId,
        ?int $academicYearId,
        string $effectiveFrom,
        ?string $effectiveTo,
        ?int $excludeId = null
    ): void {
        $query = $this->model
            ->where('school_id', $schoolId)
            ->where('subject_id', $subjectId)
            ->where('program_id', $programId)
            ->where('grade_level_id', $gradeLevelId)
            ->where('academic_year_id', $academicYearId)
            ->where('effective_from', '<=', $effectiveTo ?? $effectiveFrom);

        if ($effectiveTo) {
            $query->where(function ($q) use ($effectiveFrom) {
                $q->where('effective_to', '>=', $effectiveFrom)
                    ->orWhereNull('effective_to');
            });
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'effective_from' => 'The effective date range overlaps with an existing coefficient for this context.',
            ]);
        }
    }
}
