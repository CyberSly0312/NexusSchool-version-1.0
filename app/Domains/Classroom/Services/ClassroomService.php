<?php

declare(strict_types=1);

namespace App\Domains\Classroom\Services;

use App\Domains\Classroom\DTOs\ClassroomData;
use App\Domains\Classroom\Enums\ClassroomStatus;
use App\Domains\Classroom\Events\ClassroomArchived;
use App\Domains\Classroom\Events\ClassroomAssigned;
use App\Domains\Classroom\Events\ClassroomCreated;
use App\Domains\Classroom\Events\ClassroomReleased;
use App\Domains\Classroom\Events\ClassroomUpdated;
use App\Domains\Classroom\Rules\ClassroomRules;
use App\Models\AcademicClass;
use App\Models\Classroom;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClassroomService
{
    public function __construct(
        protected Classroom $model = new Classroom(),
    ) {
    }

    public function create(ClassroomData $data): Classroom
    {
        $validated = ClassroomRules::validate($data->toArray(), 'create');

        $this->validateUniqueNamePerSchool($validated['school_id'], $validated['name']);
        $this->validateUniqueCodePerSchool($validated['school_id'], $validated['code']);
        $this->validateCapacity($validated['capacity']);

        return DB::transaction(function () use ($validated) {
            $classroom = $this->model->create($validated);

            ClassroomCreated::dispatch($classroom);

            return $classroom;
        });
    }

    public function update(Classroom $classroom, array $data): Classroom
    {
        $validated = ClassroomRules::validate($data, 'update', $classroom->id);

        $schoolId = $validated['school_id'] ?? $classroom->school_id;
        $name = $validated['name'] ?? $classroom->name;
        $code = $validated['code'] ?? $classroom->code;

        $this->validateUniqueNamePerSchool($schoolId, $name, $classroom->id);
        $this->validateUniqueCodePerSchool($schoolId, $code, $classroom->id);

        if (isset($validated['capacity'])) {
            $this->validateCapacity($validated['capacity']);
        }

        $classroom->update($validated);

        ClassroomUpdated::dispatch($classroom->refresh());

        return $classroom->refresh();
    }

    public function delete(Classroom $classroom): bool
    {
        $classroom->delete();

        return true;
    }

    public function archive(Classroom $classroom): Classroom
    {
        $classroom->update(['status' => ClassroomStatus::Archived]);

        ClassroomArchived::dispatch($classroom->fresh());

        return $classroom->refresh();
    }

    public function activate(Classroom $classroom): Classroom
    {
        $classroom->update(['status' => ClassroomStatus::Active]);

        ClassroomUpdated::dispatch($classroom->fresh());

        return $classroom->refresh();
    }

    public function assign(Classroom $classroom, AcademicClass $academicClass): Classroom
    {
        if (! $classroom->isActive()) {
            throw ValidationException::withMessages([
                'classroom_id' => 'Only active classrooms can be assigned to academic classes.',
            ]);
        }

        $this->validateClassroomCapacity($classroom, $academicClass);

        $academicClass->update(['classroom_id' => $classroom->id]);

        ClassroomAssigned::dispatch($classroom->fresh(), $academicClass->fresh());

        return $classroom->refresh();
    }

    public function release(Classroom $classroom): Classroom
    {
        $academicClass = $classroom->academicClasses()->first();

        if ($academicClass) {
            $academicClass->update(['classroom_id' => null]);

            ClassroomReleased::dispatch($classroom->fresh(), $academicClass->fresh());
        }

        return $classroom->refresh();
    }

    public function findById(int $id): Classroom
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
                    ->orWhere('building', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['room_type'])) {
            $query->where('room_type', $filters['room_type']);
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
                'name' => 'A classroom with this name already exists in this school.',
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
                'code' => 'A classroom with this code already exists in this school.',
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

    private function validateClassroomCapacity(Classroom $classroom, AcademicClass $academicClass): void
    {
        if ($classroom->capacity < $academicClass->capacity) {
            throw ValidationException::withMessages([
                'capacity' => 'Classroom capacity ('.$classroom->capacity.') cannot be lower than the assigned academic class capacity ('.$academicClass->capacity.').',
            ]);
        }
    }
}
