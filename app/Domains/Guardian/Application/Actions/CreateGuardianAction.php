<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\Actions;

use App\Domains\Guardian\Application\DTOs\GuardianData;
use App\Domains\Guardian\Application\Services\GuardianNumberGenerator;
use App\Domains\Guardian\Domain\Events\GuardianCreated;
use App\Domains\Guardian\Domain\Exceptions\DuplicateGuardianException;
use App\Domains\Guardian\Domain\ValueObjects\EmailAddress;
use App\Domains\Guardian\Domain\ValueObjects\PhoneNumber;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateGuardianAction
{
    public function __construct(
        private readonly GuardianNumberGenerator $numberGenerator,
    ) {
    }

    public function execute(GuardianData $data, ?User $actor = null): Guardian
    {
        $data = $this->ensureGuardianNumber($data);

        $this->normalizeValueObjects($data);
        $this->assertNoDuplicate($data);

        $attributes = $this->normalizedAttributes($data);

        return DB::transaction(function () use ($attributes, $actor): Guardian {
            $guardian = Guardian::create($attributes);

            GuardianCreated::dispatch($guardian, $actor);

            return $guardian;
        });
    }

    private function ensureGuardianNumber(GuardianData $data): GuardianData
    {
        if ($data->guardianNumber !== null) {
            return $data;
        }

        return $data->withGuardianNumber($this->numberGenerator->generate($data->schoolId));
    }

    private function normalizeValueObjects(GuardianData $data): void
    {
        if ($data->email !== null) {
            new EmailAddress($data->email);
        }

        if ($data->phoneNumber !== null) {
            new PhoneNumber($data->phoneNumber);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizedAttributes(GuardianData $data): array
    {
        $attributes = $data->toArray();

        if ($attributes['email'] !== null) {
            $attributes['email'] = (new EmailAddress((string) $attributes['email']))->value();
        }

        if ($attributes['phone_number'] !== null) {
            $attributes['phone_number'] = (new PhoneNumber((string) $attributes['phone_number']))->value();
        }

        return $attributes;
    }

    private function assertNoDuplicate(GuardianData $data): void
    {
        $numberTaken = Guardian::query()
            ->forSchool($data->schoolId)
            ->where('guardian_number', $data->guardianNumber)
            ->exists();

        if ($numberTaken) {
            throw DuplicateGuardianException::guardianNumber($data->guardianNumber);
        }

        if ($data->phoneNumber !== null) {
            $phoneTaken = Guardian::query()
                ->forSchool($data->schoolId)
                ->where('phone_number', $data->phoneNumber)
                ->exists();

            if ($phoneTaken) {
                throw DuplicateGuardianException::phone($data->phoneNumber);
            }
        }

        if ($data->email !== null) {
            $emailTaken = Guardian::query()
                ->forSchool($data->schoolId)
                ->where('email', mb_strtolower(trim($data->email)))
                ->exists();

            if ($emailTaken) {
                throw DuplicateGuardianException::email($data->email);
            }
        }

        if ($data->userId !== null) {
            $userLinked = Guardian::query()
                ->where('user_id', $data->userId)
                ->exists();

            if ($userLinked) {
                throw DuplicateGuardianException::message('This user is already linked to another guardian.');
            }
        }
    }
}
