<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\Actions;

use App\Domains\Guardian\Domain\Events\GuardianUpdated;
use App\Domains\Guardian\Domain\Exceptions\DuplicateGuardianException;
use App\Domains\Guardian\Domain\Exceptions\GuardianStateException;
use App\Domains\Guardian\Domain\ValueObjects\EmailAddress;
use App\Domains\Guardian\Domain\ValueObjects\PhoneNumber;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateGuardianAction
{
    /**
     * @param  array<string, mixed>  $changes  Column-keyed changes; null values
     *                                         are left unchanged so partial
     *                                         updates never clobber data.
     */
    public function execute(Guardian $guardian, array $changes, ?User $actor = null): Guardian
    {
        if ($guardian->isImmutable()) {
            throw GuardianStateException::immutable($guardian->id);
        }

        $this->normalizeValueObjects($changes);
        $this->assertNoDuplicate($guardian, $changes);

        $changes = array_filter($changes, fn ($value) => $value !== null);

        return DB::transaction(function () use ($guardian, $changes, $actor): Guardian {
            if ($changes !== []) {
                $guardian->update($changes);
            }

            $guardian->refresh();

            GuardianUpdated::dispatch($guardian, $actor);

            return $guardian;
        });
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    private function normalizeValueObjects(array $changes): void
    {
        if (isset($changes['email'])) {
            new EmailAddress((string) $changes['email']);
        }

        if (isset($changes['phone_number'])) {
            new PhoneNumber((string) $changes['phone_number']);
        }
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    private function assertNoDuplicate(Guardian $guardian, array $changes): void
    {
        $guardianNumber = $changes['guardian_number'] ?? null;
        if ($guardianNumber !== null && (string) $guardianNumber !== (string) $guardian->guardian_number) {
            $numberTaken = Guardian::query()
                ->forSchool($guardian->school_id)
                ->where('guardian_number', $guardianNumber)
                ->whereKeyNot($guardian->getKey())
                ->exists();

            if ($numberTaken) {
                throw DuplicateGuardianException::guardianNumber((string) $guardianNumber);
            }
        }

        $phone = $changes['phone_number'] ?? null;
        if ($phone !== null && (string) $phone !== (string) $guardian->phone_number) {
            $phoneTaken = Guardian::query()
                ->forSchool($guardian->school_id)
                ->where('phone_number', $phone)
                ->whereKeyNot($guardian->getKey())
                ->exists();

            if ($phoneTaken) {
                throw DuplicateGuardianException::phone((string) $phone);
            }
        }

        $email = $changes['email'] ?? null;
        if ($email !== null && mb_strtolower(trim((string) $email)) !== mb_strtolower(trim((string) $guardian->email))) {
            $emailTaken = Guardian::query()
                ->forSchool($guardian->school_id)
                ->where('email', mb_strtolower(trim((string) $email)))
                ->whereKeyNot($guardian->getKey())
                ->exists();

            if ($emailTaken) {
                throw DuplicateGuardianException::email((string) $email);
            }
        }
    }
}
