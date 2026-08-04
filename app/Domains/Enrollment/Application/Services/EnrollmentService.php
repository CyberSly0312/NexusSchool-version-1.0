<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\Services;

use App\Domains\Enrollment\Application\Actions\CreateEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\GraduateStudentAction;
use App\Domains\Enrollment\Application\Actions\PromoteStudentAction;
use App\Domains\Enrollment\Application\Actions\RepeatStudentAction;
use App\Domains\Enrollment\Application\Actions\RestoreEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\SuspendEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\TransferEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\UpdateEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\WithdrawStudentAction;
use App\Domains\Enrollment\Application\DTOs\CreateEnrollmentData;
use App\Domains\Enrollment\Application\DTOs\GraduationData;
use App\Domains\Enrollment\Application\DTOs\PromotionData;
use App\Domains\Enrollment\Application\DTOs\TransferData;
use App\Domains\Enrollment\Application\DTOs\UpdateEnrollmentData;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Facade over the Enrollment application actions. This is the only public entry
 * point the presentation layer should use.
 */
final class EnrollmentService
{
    public function __construct(
        private readonly CreateEnrollmentAction $createEnrollment,
        private readonly UpdateEnrollmentAction $updateEnrollment,
        private readonly PromoteStudentAction $promoteStudent,
        private readonly RepeatStudentAction $repeatStudent,
        private readonly TransferEnrollmentAction $transferEnrollment,
        private readonly GraduateStudentAction $graduateStudent,
        private readonly WithdrawStudentAction $withdrawStudent,
        private readonly SuspendEnrollmentAction $suspendEnrollment,
        private readonly RestoreEnrollmentAction $restoreEnrollment,
    ) {
    }

    public function enroll(CreateEnrollmentData $data, ?User $actor = null): Enrollment
    {
        return $this->createEnrollment->execute($data, $actor);
    }

    public function update(Enrollment $enrollment, UpdateEnrollmentData $data, ?User $actor = null): Enrollment
    {
        return $this->updateEnrollment->execute($enrollment, $data, $actor);
    }

    public function promote(Enrollment $enrollment, PromotionData $data, ?User $actor = null): Enrollment
    {
        return $this->promoteStudent->execute($enrollment, $data, $actor);
    }

    public function repeat(Enrollment $enrollment, PromotionData $data, ?User $actor = null): Enrollment
    {
        return $this->repeatStudent->execute($enrollment, $data, $actor);
    }

    public function transfer(Enrollment $enrollment, TransferData $data, ?User $actor = null): Enrollment
    {
        return $this->transferEnrollment->execute($enrollment, $data, $actor);
    }

    public function graduate(Enrollment $enrollment, GraduationData $data, ?User $actor = null): Enrollment
    {
        return $this->graduateStudent->execute($enrollment, $data, $actor);
    }

    public function withdraw(Enrollment $enrollment, ?string $reason = null, ?User $actor = null): Enrollment
    {
        return $this->withdrawStudent->execute($enrollment, $reason, null, $actor);
    }

    public function suspend(
        Enrollment $enrollment,
        ?string $reason = null,
        ?string $until = null,
        ?User $actor = null,
    ): Enrollment {
        return $this->suspendEnrollment->execute($enrollment, $reason, $until, $actor);
    }

    public function restore(Enrollment $enrollment, ?User $actor = null): Enrollment
    {
        return $this->restoreEnrollment->execute($enrollment, $actor);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Enrollment::query();

        if (isset($filters['school_id'])) {
            $query->forSchool($filters['school_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (isset($filters['academic_calendar_year_id'])) {
            $query->where('academic_calendar_year_id', $filters['academic_calendar_year_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('enrollment_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($student) use ($search): void {
                        $student->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('student_number', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }
}
