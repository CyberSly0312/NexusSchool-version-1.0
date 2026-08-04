<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\Actions;

use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Events\EnrollmentRestored;
use App\Domains\Enrollment\Domain\Exceptions\EnrollmentStateException;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class RestoreEnrollmentAction
{
    public function execute(Enrollment $enrollment, ?User $actor = null): Enrollment
    {
        if ($enrollment->isImmutable() || ! $enrollment->isSuspended()) {
            throw EnrollmentStateException::transition($enrollment->status, 'active');
        }

        return DB::transaction(function () use ($enrollment, $actor): Enrollment {
            $enrollment->status = EnrollmentStatus::Active;
            $enrollment->suspension_reason = null;
            $enrollment->suspended_until = null;
            $enrollment->save();

            EnrollmentRestored::dispatch($enrollment, $actor);

            return $enrollment;
        });
    }
}
