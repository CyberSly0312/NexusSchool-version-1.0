<?php

declare(strict_types=1);

namespace App\Domains\Student\Services;

use App\Domains\Student\DTOs\StudentData;
use App\Domains\Student\Enums\StudentStatus;
use App\Domains\Student\Events\StudentActivated;
use App\Domains\Student\Events\StudentArchived;
use App\Domains\Student\Events\StudentCreated;
use App\Domains\Student\Events\StudentUpdated;
use App\Domains\Student\Rules\StudentRules;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentService
{
    public function __construct(
        protected Student $model = new Student(),
    ) {
    }

    public function create(StudentData $data): Student
    {
        $validated = StudentRules::validate($data->toArray(), 'create');

        $this->validateUniqueStudentNumberPerSchool($validated['school_id'], $validated['student_number']);

        if (! empty($validated['admission_number'])) {
            $this->validateUniqueAdmissionNumberPerSchool($validated['school_id'], $validated['admission_number']);
        }

        if (! empty($validated['user_id'])) {
            $this->validateUserNotLinked($validated['user_id']);
        }

        return DB::transaction(function () use ($validated) {
            $student = $this->model->create($validated);

            StudentCreated::dispatch($student);

            return $student;
        });
    }

    public function update(Student $student, array $data): Student
    {
        $validated = StudentRules::validate($data, 'update', $student->id);

        $schoolId = $validated['school_id'] ?? $student->school_id;

        if (isset($validated['student_number'])) {
            $this->validateUniqueStudentNumberPerSchool($schoolId, $validated['student_number'], $student->id);
        }

        if (isset($validated['admission_number']) && ! empty($validated['admission_number'])) {
            $this->validateUniqueAdmissionNumberPerSchool($schoolId, $validated['admission_number'], $student->id);
        }

        if (isset($validated['user_id'])) {
            $this->validateUserNotLinked($validated['user_id'], $student->id);
        }

        $student->update($validated);

        StudentUpdated::dispatch($student->refresh());

        return $student->refresh();
    }

    public function delete(Student $student): bool
    {
        $student->delete();

        return true;
    }

    public function archive(Student $student): Student
    {
        $student->update(['status' => StudentStatus::Archived]);

        StudentArchived::dispatch($student->fresh());

        return $student->refresh();
    }

    public function activate(Student $student): Student
    {
        $student->update(['status' => StudentStatus::Active]);

        StudentActivated::dispatch($student->fresh());

        return $student->refresh();
    }

    public function findById(int $id): Student
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        return $query->orderBy('last_name')->orderBy('first_name')->paginate($perPage);
    }

    private function validateUniqueStudentNumberPerSchool(string $schoolId, string $studentNumber, ?int $excludeId = null): void
    {
        $query = $this->model
            ->where('school_id', $schoolId)
            ->where('student_number', $studentNumber);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'student_number' => 'A student with this number already exists in this school.',
            ]);
        }
    }

    private function validateUniqueAdmissionNumberPerSchool(string $schoolId, string $admissionNumber, ?int $excludeId = null): void
    {
        $query = $this->model
            ->where('school_id', $schoolId)
            ->where('admission_number', $admissionNumber);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'admission_number' => 'A student with this admission number already exists in this school.',
            ]);
        }
    }

    private function validateUserNotLinked(int $userId, ?int $excludeStudentId = null): void
    {
        $query = $this->model->where('user_id', $userId);

        if ($excludeStudentId) {
            $query->where('id', '!=', $excludeStudentId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'This user is already linked to another student.',
            ]);
        }
    }
}
