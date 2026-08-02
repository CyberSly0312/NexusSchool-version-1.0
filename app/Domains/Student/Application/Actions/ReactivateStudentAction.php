<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\Actions;

use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Events\StudentReactivated;
use App\Domains\Student\Domain\Exceptions\InvalidStudentStateException;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class ReactivateStudentAction
{
    public function execute(Student $student, ?User $actor = null): Student
    {
        if (! $student->isSuspended() && ! $student->isWithdrawn() && ! $student->isInactive()) {
            throw InvalidStudentStateException::for(
                'reactivate',
                $student->status->value,
                'Only suspended, withdrawn or inactive students can be reactivated.',
            );
        }

        return DB::transaction(function () use ($student, $actor): Student {
            $student->update([
                'status' => StudentStatus::Active,
                'reactivated_at' => now(),
                'suspended_until' => null,
            ]);
            $student->refresh();

            StudentReactivated::dispatch($student, $actor);

            return $student;
        });
    }
}
