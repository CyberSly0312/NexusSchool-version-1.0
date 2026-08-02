<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\Services;

use App\Domains\Student\Application\Actions\ArchiveStudentAction;
use App\Domains\Student\Application\Actions\CreateStudentAction;
use App\Domains\Student\Application\Actions\GraduateStudentAction;
use App\Domains\Student\Application\Actions\ReactivateStudentAction;
use App\Domains\Student\Application\Actions\RestoreStudentAction;
use App\Domains\Student\Application\Actions\SuspendStudentAction;
use App\Domains\Student\Application\Actions\TransferStudentAction;
use App\Domains\Student\Application\Actions\UpdateStudentAction;
use App\Domains\Student\Application\Actions\WithdrawStudentAction;
use App\Domains\Student\Application\DTOs\CreateStudentData;
use App\Domains\Student\Application\DTOs\TransferStudentData;
use App\Domains\Student\Application\DTOs\UpdateStudentData;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Facade over the Student application actions. This is the only public entry
 * point the presentation layer should use.
 */
final class StudentService
{
    public function __construct(
        private readonly CreateStudentAction $createStudent,
        private readonly UpdateStudentAction $updateStudent,
        private readonly TransferStudentAction $transferStudent,
        private readonly SuspendStudentAction $suspendStudent,
        private readonly GraduateStudentAction $graduateStudent,
        private readonly WithdrawStudentAction $withdrawStudent,
        private readonly ReactivateStudentAction $reactivateStudent,
        private readonly ArchiveStudentAction $archiveStudent,
        private readonly RestoreStudentAction $restoreStudent,
    ) {
    }

    public function register(CreateStudentData $data, ?User $actor = null): Student
    {
        return $this->createStudent->execute($data, $actor);
    }

    public function update(Student $student, UpdateStudentData $data, ?User $actor = null): Student
    {
        return $this->updateStudent->execute($student, $data, $actor);
    }

    public function transfer(Student $student, TransferStudentData $data, ?User $actor = null): Student
    {
        return $this->transferStudent->execute($student, $data, $actor);
    }

    public function suspend(Student $student, ?string $reason = null, ?string $until = null, ?User $actor = null): Student
    {
        return $this->suspendStudent->execute($student, $reason, $until, $actor);
    }

    public function graduate(Student $student, ?User $actor = null): Student
    {
        return $this->graduateStudent->execute($student, $actor);
    }

    public function withdraw(Student $student, ?string $reason = null, ?User $actor = null): Student
    {
        return $this->withdrawStudent->execute($student, $reason, $actor);
    }

    public function reactivate(Student $student, ?User $actor = null): Student
    {
        return $this->reactivateStudent->execute($student, $actor);
    }

    public function archive(Student $student, ?User $actor = null): Student
    {
        return $this->archiveStudent->execute($student, $actor);
    }

    public function restore(Student $student, ?User $actor = null): Student
    {
        return $this->restoreStudent->execute($student, $actor);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Student::query();

        if (isset($filters['school_id'])) {
            $query->forSchool($filters['school_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('last_name')->orderBy('first_name')->paginate($perPage);
    }
}
