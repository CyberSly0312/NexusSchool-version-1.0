<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\Actions;

use App\Domains\Staff\Domain\Enums\StaffStatus;
use App\Domains\Staff\Domain\Events\StaffRestored;
use App\Domains\Staff\Domain\Exceptions\InvalidStaffStateException;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Restores a suspended staff member to active duty.
 */
final class RestoreStaffAction
{
    public function execute(Staff $staff, ?User $actor = null): Staff
    {
        $this->assertCanBeRestored($staff);

        return DB::transaction(function () use ($staff, $actor): Staff {
            $staff->update(['status' => StaffStatus::Active]);

            StaffRestored::dispatch($staff, $actor);

            return $staff;
        });
    }

    private function assertCanBeRestored(Staff $staff): void
    {
        if ($staff->status !== StaffStatus::Suspended) {
            throw InvalidStaffStateException::transition($staff->status, StaffStatus::Active->value);
        }
    }
}
