<?php

declare(strict_types=1);

namespace App\Domains\StudentEnrollment\Services;

use App\Domains\StudentEnrollment\DTOs\StudentEnrollmentData;
use App\Domains\StudentEnrollment\Enums\EnrollmentStatus;
use App\Domains\StudentEnrollment\Enums\EnrollmentType;
use App\Domains\StudentEnrollment\Events\EnrollmentActivated;
use App\Domains\StudentEnrollment\Events\EnrollmentCompleted;
use App\Domains\StudentEnrollment\Events\EnrollmentSuspended;
use App\Domains\StudentEnrollment\Events\EnrollmentWithdrawn;
use App\Domains\StudentEnrollment\Events\StudentEnrolled;
use App\Domains\StudentEnrollment\Rules\StudentEnrollmentRules;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentEnrollmentService
{
    public function __construct(
        protected StudentEnrollment $model = new StudentEnrollment(),
    ) {
    }

    public function create(StudentEnrollmentData $data): StudentEnrollment
    {
        $validated = StudentEnrollmentRules::validate($data->toArray(), 'create');

        $this->validateStudentActive($validated['student_id']);
        $this->validateNoDuplicateActiveEnrollment($validated['student_id'], $validated['academic_year_id']);
        $this->validateUniqueEnrollmentNumberPerSchool($validated['school_id'], $validated['enrollment_number']);

        return DB::transaction(function () use ($validated) {
            $enrollment = $this->model->create($validated);

            StudentEnrolled::dispatch($enrollment);

            return $enrollment;
        });
    }

    public function update(StudentEnrollment $enrollment, array $data): StudentEnrollment
    {
        if ($enrollment->status === EnrollmentStatus::Completed || $enrollment->status === EnrollmentStatus::Cancelled || $enrollment->status === EnrollmentStatus::Withdrawn) {
            throw ValidationException::withMessages([
                'status' => 'Archived enrollments are read-only and cannot be modified.',
            ]);
        }

        $validated = StudentEnrollmentRules::validate($data, 'update', $enrollment->id);

        $schoolId = $validated['school_id'] ?? $enrollment->school_id;

        if (isset($validated['enrollment_number'])) {
            $this->validateUniqueEnrollmentNumberPerSchool($schoolId, $validated['enrollment_number'], $enrollment->id);
        }

        if (isset($validated['student_id']) && $validated['student_id'] !== $enrollment->student_id) {
            $this->validateStudentActive($validated['student_id']);
        }

        if (isset($validated['exit_date'], $validated['enrollment_date']) && $validated['exit_date'] < $validated['enrollment_date']) {
            throw ValidationException::withMessages([
                'exit_date' => 'Exit date cannot precede the enrollment date.',
            ]);
        }

        $enrollment->update($validated);

        return $enrollment->refresh();
    }

    public function delete(StudentEnrollment $enrollment): bool
    {
        $enrollment->delete();

        return true;
    }

    public function activate(StudentEnrollment $enrollment): StudentEnrollment
    {
        $this->validateTransition($enrollment, EnrollmentStatus::Active);

        $enrollment->update(['status' => EnrollmentStatus::Active]);

        EnrollmentActivated::dispatch($enrollment->fresh());

        return $enrollment->refresh();
    }

    public function suspend(StudentEnrollment $enrollment): StudentEnrollment
    {
        $this->validateTransition($enrollment, EnrollmentStatus::Suspended);

        $enrollment->update(['status' => EnrollmentStatus::Suspended]);

        EnrollmentSuspended::dispatch($enrollment->fresh());

        return $enrollment->refresh();
    }

    public function complete(StudentEnrollment $enrollment): StudentEnrollment
    {
        $this->validateTransition($enrollment, EnrollmentStatus::Completed);

        $enrollment->update([
            'status' => EnrollmentStatus::Completed,
            'exit_date' => now()->format('Y-m-d'),
            'exit_reason' => EnrollmentType::Promotion->label(),
        ]);

        EnrollmentCompleted::dispatch($enrollment->fresh());

        return $enrollment->refresh();
    }

    public function withdraw(StudentEnrollment $enrollment, ?string $reason = null): StudentEnrollment
    {
        $this->validateTransition($enrollment, EnrollmentStatus::Withdrawn);

        $enrollment->update([
            'status' => EnrollmentStatus::Withdrawn,
            'exit_date' => now()->format('Y-m-d'),
            'exit_reason' => $reason,
        ]);

        EnrollmentWithdrawn::dispatch($enrollment->fresh());

        return $enrollment->refresh();
    }

    public function findById(int $id): StudentEnrollment
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['student', 'academicYear', 'academicClass', 'program', 'gradeLevel', 'section']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('enrollment_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (isset($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (isset($filters['enrollment_type'])) {
            $query->where('enrollment_type', $filters['enrollment_type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getActiveEnrollments(int $studentId, int $academicYearId): ?StudentEnrollment
    {
        return $this->model
            ->where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', EnrollmentStatus::Active)
            ->first();
    }

    private function validateTransition(StudentEnrollment $enrollment, EnrollmentStatus $target): void
    {
        if (! $enrollment->canTransitionTo($target)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition from '{$enrollment->status->label()}' to '{$target->label()}'.",
            ]);
        }
    }

    private function validateStudentActive(int $studentId): void
    {
        $student = Student::find($studentId);

        if (! $student) {
            throw ValidationException::withMessages([
                'student_id' => 'Student not found.',
            ]);
        }

        if (! $student->isActive()) {
            throw ValidationException::withMessages([
                'student_id' => 'Only active students may receive new enrollments.',
            ]);
        }
    }

    private function validateNoDuplicateActiveEnrollment(int $studentId, int $academicYearId): void
    {
        $existing = $this->getActiveEnrollments($studentId, $academicYearId);

        if ($existing) {
            throw ValidationException::withMessages([
                'academic_year_id' => 'This student already has an active enrollment for this academic year.',
            ]);
        }
    }

    private function validateUniqueEnrollmentNumberPerSchool(string $schoolId, string $enrollmentNumber, ?int $excludeId = null): void
    {
        $query = $this->model
            ->where('school_id', $schoolId)
            ->where('enrollment_number', $enrollmentNumber);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'enrollment_number' => 'An enrollment with this number already exists in this school.',
            ]);
        }
    }
}
