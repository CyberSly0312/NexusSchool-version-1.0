<?php

declare(strict_types=1);

namespace App\Domains\AcademicClass\Services;

use App\Domains\AcademicClass\DTOs\AcademicClassData;
use App\Domains\AcademicClass\Enums\AcademicClassStatus;
use App\Domains\AcademicClass\Events\AcademicClassActivated;
use App\Domains\AcademicClass\Events\AcademicClassArchived;
use App\Domains\AcademicClass\Events\AcademicClassCreated;
use App\Domains\AcademicClass\Events\AcademicClassUpdated;
use App\Domains\AcademicClass\Rules\AcademicClassRules;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AcademicClassService
{
    public function __construct(
        protected AcademicClass $model = new AcademicClass(),
    ) {
    }

    public function create(AcademicClassData $data): AcademicClass
    {
        $validated = AcademicClassRules::validate($data->toArray(), 'create');

        $this->validateUniqueNamePerYear($validated['academic_year_id'], $validated['name']);
        $this->validateUniqueCodePerSchool($validated['academic_year_id'], $validated['code']);
        $this->validateCapacity($validated['capacity']);

        return DB::transaction(function () use ($validated) {
            $academicClass = $this->model->create($validated);

            AcademicClassCreated::dispatch($academicClass);

            return $academicClass;
        });
    }

    public function update(AcademicClass $academicClass, array $data): AcademicClass
    {
        $validated = AcademicClassRules::validate($data, 'update', $academicClass->id);

        $academicYearId = $validated['academic_year_id'] ?? $academicClass->academic_year_id;
        $name = $validated['name'] ?? $academicClass->name;
        $code = $validated['code'] ?? $academicClass->code;

        $this->validateUniqueNamePerYear($academicYearId, $name, $academicClass->id);
        $this->validateUniqueCodePerSchool($academicYearId, $code, $academicClass->id);

        if (isset($validated['capacity'])) {
            $this->validateCapacity($validated['capacity']);
        }

        $academicClass->update($validated);

        AcademicClassUpdated::dispatch($academicClass->refresh());

        return $academicClass->refresh();
    }

    public function delete(AcademicClass $academicClass): bool
    {
        $academicClass->delete();

        return true;
    }

    public function archive(AcademicClass $academicClass): AcademicClass
    {
        $academicClass->update(['status' => AcademicClassStatus::Archived]);

        AcademicClassArchived::dispatch($academicClass->fresh());

        return $academicClass->refresh();
    }

    public function activate(AcademicClass $academicClass): AcademicClass
    {
        $academicClass->update(['status' => AcademicClassStatus::Active]);

        AcademicClassActivated::dispatch($academicClass->fresh());

        return $academicClass->refresh();
    }

    public function toggleActive(AcademicClass $academicClass): AcademicClass
    {
        $academicClass->update(['is_active' => ! $academicClass->is_active]);

        AcademicClassUpdated::dispatch($academicClass->refresh());

        return $academicClass->refresh();
    }

    public function findById(int $id): AcademicClass
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

        if (isset($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (isset($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

        if (isset($filters['grade_level_id'])) {
            $query->where('grade_level_id', $filters['grade_level_id']);
        }

        if (isset($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        return $query->orderBy('display_order')->orderBy('name')->paginate($perPage);
    }

    private function validateUniqueNamePerYear(int $academicYearId, string $name, ?int $excludeId = null): void
    {
        $query = $this->model
            ->where('academic_year_id', $academicYearId)
            ->where('name', $name);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A class with this name already exists in this academic year.',
            ]);
        }
    }

    private function validateUniqueCodePerSchool(int $academicYearId, string $code, ?int $excludeId = null): void
    {
        $academicYear = AcademicYear::findOrFail($academicYearId);

        $yearIds = AcademicYear::where('school_id', $academicYear->school_id)
            ->pluck('id');

        $query = $this->model
            ->whereIn('academic_year_id', $yearIds)
            ->where('code', $code);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'code' => 'A class with this code already exists in this school.',
            ]);
        }
    }

    private function validateCapacity(int $capacity): void
    {
        if ($capacity < 1) {
            throw ValidationException::withMessages([
                'capacity' => 'Capacity must be greater than zero.',
            ]);
        }
    }
}
