<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\Actions;

use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Events\StudentWithdrawn;
use App\Domains\Enrollment\Domain\Exceptions\EnrollmentStateException;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class WithdrawStudentAction
{
    public function execute(
        Enrollment $enrollment,
        ?string $reason = null,
        ?string $exitedAt = null,
        ?User $actor = null,
    ): Enrollment {
        if ($enrollment->isImmutable() || (! $enrollment->isActive() && ! $enrollment->isSuspended())) {
            throw EnrollmentStateException::transition($enrollment->status, 'withdrawn');
        }

        return DB::transaction(function () use ($enrollment, $reason, $exitedAt, $actor): Enrollment {
            $enrollment->status = EnrollmentStatus::Withdrawn;
            $enrollment->exit_reason = $reason;
            $enrollment->exited_at = $exitedAt ?? $enrollment->exited_at?->toDateString() ?? now()->toDateString();
            $enrollment->save();

            StudentWithdrawn::dispatch($enrollment, $actor);

            return $enrollment;
        });
    }
}
