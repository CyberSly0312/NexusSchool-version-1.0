<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\Actions;

use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Events\StudentSuspended;
use App\Domains\Student\Domain\Exceptions\InvalidStudentStateException;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class SuspendStudentAction
{
    public function execute(Student $student, ?string $reason = null, ?string $until = null, ?User $actor = null): Student
    {
        if (! $student->isActive()) {
            throw InvalidStudentStateException::for(
                'suspend',
                $student->status->value,
                'Only active students can be suspended.',
            );
        }

        return DB::transaction(function () use ($student, $reason, $until, $actor): Student {
            $student->update([
                'status' => StudentStatus::Suspended,
                'suspended_at' => now(),
                'suspended_until' => $until !== null ? $until : null,
                'suspension_reason' => $reason,
            ]);
            $student->refresh();

            StudentSuspended::dispatch($student, $actor);

            return $student;
        });
    }
}
