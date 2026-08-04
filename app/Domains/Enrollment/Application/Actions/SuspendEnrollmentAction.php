<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\Actions;

use App\Domains\Enrollment\Domain\Events\EnrollmentSuspended;
use App\Domains\Enrollment\Domain\Exceptions\EnrollmentStateException;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class SuspendEnrollmentAction
{
    public function execute(
        Enrollment $enrollment,
        ?string $reason = null,
        ?string $until = null,
        ?User $actor = null,
    ): Enrollment {
        if ($enrollment->isImmutable() || ! $enrollment->isActive()) {
            throw EnrollmentStateException::transition($enrollment->status, 'suspended');
        }

        return DB::transaction(function () use ($enrollment, $reason, $until, $actor): Enrollment {
            $enrollment->status = \App\Domains\Enrollment\Domain\Enums\EnrollmentStatus::Suspended;
            $enrollment->suspension_reason = $reason;
            $enrollment->suspended_until = $until;
            $enrollment->save();

            EnrollmentSuspended::dispatch($enrollment, $actor);

            return $enrollment;
        });
    }
}
