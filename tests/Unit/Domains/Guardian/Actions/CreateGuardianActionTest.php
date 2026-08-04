<?php

declare(strict_types=1);

use App\Domains\Guardian\Application\Actions\CreateGuardianAction;
use App\Domains\Guardian\Application\DTOs\GuardianData;
use App\Domains\Guardian\Domain\Exceptions\DuplicateGuardianException;
use App\Models\User;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->action = app(CreateGuardianAction::class);
});

function guardianData(string $schoolId, array $overrides = []): GuardianData
{
    return GuardianData::fromArray(array_merge([
        'school_id' => $schoolId,
        'first_name' => 'Marie',
        'last_name' => 'Dupont',
        'gender' => 'female',
        'phone_number' => '+237690123456',
        'email' => 'marie@example.com',
    ], $overrides));
}

test('creating a guardian assigns an auto-generated number and persists', function (): void {
    $guardian = $this->action->execute(guardianData($this->schoolId));

    expect($guardian->exists)->toBeTrue()
        ->and($guardian->guardian_number)->toMatch('/^GUA-[0-9A-F]{6}-0001$/')
        ->and($guardian->school_id)->toBe($this->schoolId)
        ->and($guardian->full_name)->toBe('Marie Dupont');
});

test('creating a guardian normalizes the email to lowercase', function (): void {
    $guardian = $this->action->execute(guardianData($this->schoolId, ['email' => 'Marie@Example.COM']));

    expect($guardian->email)->toBe('marie@example.com');
});

test('creating a guardian rejects a malformed email', function (): void {
    $this->action->execute(guardianData($this->schoolId, ['email' => 'not-an-email']));
})->throws(InvalidArgumentException::class);

test('creating a guardian rejects a malformed phone number', function (): void {
    $this->action->execute(guardianData($this->schoolId, ['phone_number' => 'abc']));
})->throws(InvalidArgumentException::class);

test('creating a guardian rejects a duplicate phone within the school', function (): void {
    $this->action->execute(guardianData($this->schoolId));

    $this->action->execute(guardianData($this->schoolId, ['first_name' => 'Jean', 'email' => 'jean@example.com']));
})->throws(DuplicateGuardianException::class);

test('creating a guardian rejects a duplicate email within the school', function (): void {
    $this->action->execute(guardianData($this->schoolId));

    $this->action->execute(guardianData($this->schoolId, ['first_name' => 'Jean', 'phone_number' => '+237699999999']));
})->throws(DuplicateGuardianException::class);

test('creating a guardian accepts the same phone in another school', function (): void {
    $otherSchool = makeTenant();

    $this->action->execute(guardianData($this->schoolId));

    $guardian = $this->action->execute(guardianData($otherSchool, ['email' => 'other@example.com']));

    expect($guardian->school_id)->toBe($otherSchool);
});

test('creating a guardian rejects a duplicate guardian number', function (): void {
    $first = $this->action->execute(guardianData($this->schoolId));

    $this->action->execute(guardianData($this->schoolId, [
        'guardian_number' => $first->guardian_number,
        'phone_number' => '+237699999999',
        'email' => 'jean@example.com',
    ]));
})->throws(DuplicateGuardianException::class);

test('creating a guardian rejects a user already linked to another guardian', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    $this->action->execute(guardianData($this->schoolId, ['user_id' => $user->id]));

    $this->action->execute(guardianData($this->schoolId, [
        'first_name' => 'Jean',
        'phone_number' => '+237699999999',
        'email' => 'jean@example.com',
        'user_id' => $user->id,
    ]));
})->throws(DuplicateGuardianException::class);
