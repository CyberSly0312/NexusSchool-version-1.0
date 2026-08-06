<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\Actions;

use App\Domains\Staff\Domain\Events\StaffUpdated;
use App\Domains\Staff\Domain\Exceptions\DuplicateStaffException;
use App\Domains\Staff\Domain\Exceptions\InvalidStaffStateException;
use App\Domains\Staff\Domain\ValueObjects\BankAccount;
use App\Domains\Staff\Domain\ValueObjects\EmailAddress;
use App\Domains\Staff\Domain\ValueObjects\PhoneNumber;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Models\User;

/**
 * Updates the mutable attributes of a Staff aggregate. The professional
 * identity may be edited while the staff member is active, inactive or
 * suspended; retired, resigned and terminated records are immutable.
 */
final class UpdateStaffAction
{
    /**
     * @param  array<string, mixed>  $changes
     */
    public function execute(Staff $staff, array $changes, ?User $actor = null): Staff
    {
        $this->assertMutable($staff);
        $this->normalizeValueObjects($changes);
        $this->assertNoDuplicate($staff, $changes);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($staff, $changes, $actor): Staff {
            $staff->update($this->normalizedAttributes($changes));

            StaffUpdated::dispatch($staff, $actor);

            return $staff;
        });
    }

    private function assertMutable(Staff $staff): void
    {
        if ($staff->isImmutable()) {
            throw InvalidStaffStateException::immutable($staff->getKey());
        }
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    private function normalizeValueObjects(array $changes): void
    {
        if (isset($changes['email']) && $changes['email'] !== null) {
            new EmailAddress((string) $changes['email']);
        }

        if (isset($changes['phone_number']) && $changes['phone_number'] !== null) {
            new PhoneNumber((string) $changes['phone_number']);
        }

        if (isset($changes['bank_account']) && $changes['bank_account'] !== null) {
            new BankAccount((string) $changes['bank_account']);
        }
    }

    /**
     * @param  array<string, mixed>  $changes
     * @return array<string, mixed>
     */
    private function normalizedAttributes(array $changes): array
    {
        if (isset($changes['email']) && $changes['email'] !== null) {
            $changes['email'] = (new EmailAddress((string) $changes['email']))->value();
        }

        if (isset($changes['phone_number']) && $changes['phone_number'] !== null) {
            $changes['phone_number'] = (new PhoneNumber((string) $changes['phone_number']))->value();
        }

        if (isset($changes['bank_account']) && $changes['bank_account'] !== null) {
            $changes['bank_account'] = (new BankAccount((string) $changes['bank_account']))->value();
        }

        return $changes;
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    private function assertNoDuplicate(Staff $staff, array $changes): void
    {
        $schoolId = $staff->school_id;

        if (isset($changes['staff_number'])) {
            $taken = Staff::query()
                ->forSchool($schoolId)
                ->where('staff_number', $changes['staff_number'])
                ->whereKeyNot($staff->getKey())
                ->exists();

            if ($taken) {
                throw DuplicateStaffException::staffNumber((string) $changes['staff_number']);
            }
        }

        if (isset($changes['phone_number'])) {
            $taken = Staff::query()
                ->forSchool($schoolId)
                ->where('phone_number', $changes['phone_number'])
                ->whereKeyNot($staff->getKey())
                ->exists();

            if ($taken) {
                throw DuplicateStaffException::phone((string) $changes['phone_number']);
            }
        }

        if (isset($changes['email'])) {
            $taken = Staff::query()
                ->forSchool($schoolId)
                ->where('email', mb_strtolower(trim((string) $changes['email'])))
                ->whereKeyNot($staff->getKey())
                ->exists();

            if ($taken) {
                throw DuplicateStaffException::email((string) $changes['email']);
            }
        }
    }
}
