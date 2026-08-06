<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\Actions;

use App\Domains\Staff\Domain\Enums\StaffStatus;
use App\Domains\Staff\Domain\Events\StaffSuspended;
use App\Domains\Staff\Domain\Exceptions\InvalidStaffStateException;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Suspends an active staff member. Suspension is reversible (see
 * RestoreStaffAction) and recorded in the audit trail.
 */
final class SuspendStaffAction
{
    public function execute(Staff $staff, ?string $reason = null, ?User $actor = null): Staff
    {
        $this->assertCanBeSuspended($staff);

        return DB::transaction(function () use ($staff, $reason, $actor): Staff {
            $staff->update(['status' => StaffStatus::Suspended]);

            StaffSuspended::dispatch($staff, $reason, $actor);

            return $staff;
        });
    }

    private function assertCanBeSuspended(Staff $staff): void
    {
        if ($staff->isImmutable()) {
            throw InvalidStaffStateException::immutable($staff->getKey());
        }

        if ($staff->status === StaffStatus::Suspended) {
            throw InvalidStaffStateException::transition($staff->status, StaffStatus::Suspended->value);
        }
    }
}
