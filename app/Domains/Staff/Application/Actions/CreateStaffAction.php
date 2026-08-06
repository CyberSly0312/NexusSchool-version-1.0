<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\Actions;

use App\Domains\Staff\Application\DTOs\CreateStaffData;
use App\Domains\Staff\Application\Services\StaffNumberGenerator;
use App\Domains\Staff\Domain\Enums\StaffSpecialization;
use App\Domains\Staff\Domain\Events\StaffCreated;
use App\Domains\Staff\Domain\Exceptions\DuplicateStaffException;
use App\Domains\Staff\Domain\ValueObjects\BankAccount;
use App\Domains\Staff\Domain\ValueObjects\EmailAddress;
use App\Domains\Staff\Domain\ValueObjects\NationalIdentifier;
use App\Domains\Staff\Domain\ValueObjects\PhoneNumber;
use App\Domains\Staff\Domain\ValueObjects\StaffNumber;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Infrastructure\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Registers a new Staff aggregate. Teacher, Director, Secretary, ... are
 * specializations of this single aggregate, never separate roots. A Teacher
 * gets its supporting TeacherProfile within the same transaction.
 */
final class CreateStaffAction
{
    public function __construct(
        private readonly StaffNumberGenerator $numberGenerator,
    ) {
    }

    public function execute(CreateStaffData $data, ?User $actor = null): Staff
    {
        $data = $this->ensureStaffNumber($data);

        $this->normalizeValueObjects($data);
        $this->assertNoDuplicate($data);

        $attributes = $this->normalizedAttributes($data);

        return DB::transaction(function () use ($attributes, $actor): Staff {
            $staff = Staff::create($attributes);

            if ($staff->specialization === StaffSpecialization::Teacher) {
                TeacherProfile::create([
                    'school_id' => $staff->school_id,
                    'staff_id' => $staff->getKey(),
                ]);
            }

            StaffCreated::dispatch($staff, $actor);

            return $staff;
        });
    }

    private function ensureStaffNumber(CreateStaffData $data): CreateStaffData
    {
        if ($data->staffNumber !== null) {
            return $data;
        }

        return $data->withStaffNumber($this->numberGenerator->generate($data->schoolId));
    }

    private function normalizeValueObjects(CreateStaffData $data): void
    {
        new StaffNumber((string) $data->staffNumber);

        if ($data->email !== null) {
            new EmailAddress($data->email);
        }

        if ($data->phoneNumber !== null) {
            new PhoneNumber($data->phoneNumber);
        }

        if ($data->nationalId !== null) {
            new NationalIdentifier($data->nationalId);
        }

        if ($data->bankAccount !== null) {
            new BankAccount($data->bankAccount);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizedAttributes(CreateStaffData $data): array
    {
        $attributes = $data->toArray();

        if ($attributes['email'] !== null) {
            $attributes['email'] = (new EmailAddress((string) $attributes['email']))->value();
        }

        if ($attributes['phone_number'] !== null) {
            $attributes['phone_number'] = (new PhoneNumber((string) $attributes['phone_number']))->value();
        }

        if ($attributes['bank_account'] !== null) {
            $attributes['bank_account'] = (new BankAccount((string) $attributes['bank_account']))->value();
        }

        return $attributes;
    }

    private function assertNoDuplicate(CreateStaffData $data): void
    {
        $numberTaken = Staff::query()
            ->forSchool($data->schoolId)
            ->where('staff_number', $data->staffNumber)
            ->exists();

        if ($numberTaken) {
            throw DuplicateStaffException::staffNumber((string) $data->staffNumber);
        }

        if ($data->phoneNumber !== null) {
            $phoneTaken = Staff::query()
                ->forSchool($data->schoolId)
                ->where('phone_number', $data->phoneNumber)
                ->exists();

            if ($phoneTaken) {
                throw DuplicateStaffException::phone($data->phoneNumber);
            }
        }

        if ($data->email !== null) {
            $emailTaken = Staff::query()
                ->forSchool($data->schoolId)
                ->where('email', mb_strtolower(trim($data->email)))
                ->exists();

            if ($emailTaken) {
                throw DuplicateStaffException::email($data->email);
            }
        }

        if ($data->nationalId !== null) {
            $nationalIdTaken = Staff::query()
                ->forSchool($data->schoolId)
                ->where('national_id', $data->nationalId)
                ->exists();

            if ($nationalIdTaken) {
                throw DuplicateStaffException::nationalId($data->nationalId);
            }
        }

        if ($data->userId !== null) {
            $userLinked = Staff::query()
                ->where('user_id', $data->userId)
                ->exists();

            if ($userLinked) {
                throw DuplicateStaffException::message('This user is already linked to another staff member.');
            }
        }
    }
}
