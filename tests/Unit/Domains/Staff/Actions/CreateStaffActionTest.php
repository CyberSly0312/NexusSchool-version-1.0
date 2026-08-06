<?php

declare(strict_types=1);

use App\Domains\Staff\Application\Actions\CreateStaffAction;
use App\Domains\Staff\Application\DTOs\CreateStaffData;
use App\Domains\Staff\Domain\Exceptions\DuplicateStaffException;
use App\Domains\Staff\Infrastructure\Models\TeacherProfile;
use App\Models\User;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->action = app(CreateStaffAction::class);
});

function staffData(string $schoolId, array $overrides = []): CreateStaffData
{
    return CreateStaffData::fromArray(array_merge([
        'school_id' => $schoolId,
        'first_name' => 'Adama',
        'last_name' => 'Diallo',
        'gender' => 'male',
        'phone_number' => '+22670112233',
        'email' => 'adama@example.com',
    ], $overrides));
}

test('creating a staff member assigns an auto-generated number and persists', function (): void {
    $staff = $this->action->execute(staffData($this->schoolId));

    expect($staff->exists)->toBeTrue()
        ->and($staff->staff_number)->toMatch('/^STF-[0-9A-F]{6}-0001$/')
        ->and($staff->school_id)->toBe($this->schoolId)
        ->and($staff->full_name)->toBe('Adama Diallo');
});

test('creating a teacher spawns its supporting teacher profile', function (): void {
    $staff = $this->action->execute(staffData($this->schoolId, ['specialization' => 'teacher']));

    expect(TeacherProfile::forSchool($this->schoolId)->where('staff_id', $staff->id)->exists())->toBeTrue();
});

test('creating a non-teacher never spawns a teacher profile', function (): void {
    $staff = $this->action->execute(staffData($this->schoolId, ['specialization' => 'secretary']));

    expect(TeacherProfile::forSchool($this->schoolId)->where('staff_id', $staff->id)->exists())->toBeFalse();
});

test('creating a staff member normalizes the email to lowercase', function (): void {
    $staff = $this->action->execute(staffData($this->schoolId, ['email' => 'Adama@Example.COM']));

    expect($staff->email)->toBe('adama@example.com');
});

test('creating a staff member rejects a malformed email', function (): void {
    $this->action->execute(staffData($this->schoolId, ['email' => 'not-an-email']));
})->throws(InvalidArgumentException::class);

test('creating a staff member rejects a malformed phone number', function (): void {
    $this->action->execute(staffData($this->schoolId, ['phone_number' => 'abc']));
})->throws(InvalidArgumentException::class);

test('creating a staff member rejects a duplicate phone within the school', function (): void {
    $this->action->execute(staffData($this->schoolId));

    $this->action->execute(staffData($this->schoolId, ['first_name' => 'Jean', 'email' => 'jean@example.com']));
})->throws(DuplicateStaffException::class);

test('creating a staff member rejects a duplicate email within the school', function (): void {
    $this->action->execute(staffData($this->schoolId));

    $this->action->execute(staffData($this->schoolId, ['first_name' => 'Jean', 'phone_number' => '+226699999999']));
})->throws(DuplicateStaffException::class);

test('creating a staff member accepts the same phone in another school', function (): void {
    $otherSchool = makeTenant();

    $this->action->execute(staffData($this->schoolId));

    $staff = $this->action->execute(staffData($otherSchool, ['email' => 'other@example.com']));

    expect($staff->school_id)->toBe($otherSchool);
});

test('creating a staff member rejects a duplicate national identifier', function (): void {
    $this->action->execute(staffData($this->schoolId, ['national_id' => 'CIN0001']));

    $this->action->execute(staffData($this->schoolId, [
        'first_name' => 'Jean',
        'phone_number' => '+226699999999',
        'email' => 'jean@example.com',
        'national_id' => 'CIN0001',
    ]));
})->throws(DuplicateStaffException::class);

test('creating a staff member rejects a duplicate staff number', function (): void {
    $first = $this->action->execute(staffData($this->schoolId));

    $this->action->execute(staffData($this->schoolId, [
        'staff_number' => $first->staff_number,
        'phone_number' => '+226699999999',
        'email' => 'jean@example.com',
    ]));
})->throws(DuplicateStaffException::class);

test('creating a staff member rejects a user already linked to another member', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    $this->action->execute(staffData($this->schoolId, ['user_id' => $user->id]));

    $this->action->execute(staffData($this->schoolId, [
        'first_name' => 'Jean',
        'phone_number' => '+226699999999',
        'email' => 'jean@example.com',
        'user_id' => $user->id,
    ]));
})->throws(DuplicateStaffException::class);
