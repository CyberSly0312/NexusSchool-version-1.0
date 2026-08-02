<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\Actions;

use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Events\StudentWithdrawn;
use App\Domains\Student\Domain\Exceptions\InvalidStudentStateException;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class WithdrawStudentAction
{
    public function execute(Student $student, ?string $reason = null, ?User $actor = null): Student
    {
        if (! $student->isActive() && ! $student->isSuspended()) {
            throw InvalidStudentStateException::for(
                'withdraw',
                $student->status->value,
                'Only active or suspended students can be withdrawn.',
            );
        }

        return DB::transaction(function () use ($student, $reason, $actor): Student {
            $student->update([
                'status' => StudentStatus::Withdrawn,
                'withdrawn_at' => now(),
                'withdrawal_reason' => $reason,
            ]);
            $student->refresh();

            StudentWithdrawn::dispatch($student, $actor);

            return $student;
        });
    }
}
