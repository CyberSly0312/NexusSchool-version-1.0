<?php

declare(strict_types=1);

use App\Domains\Staff\Application\Actions\CreateStaffAction;
use App\Domains\Staff\Application\DTOs\CreateStaffData;
use App\Domains\Staff\Domain\Exceptions\DuplicateStaffException;
use App\Domains\Staff\Infrastructure\Models\EmploymentHistory;

test('staff numbers are unique per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $first = app(CreateStaffAction::class)->execute(CreateStaffData::fromArray([
        'school_id' => $schoolA,
        'first_name' => 'Adama',
        'last_name' => 'Diallo',
        'gender' => 'male',
        'phone_number' => '+22670112288',
        'email' => 'adama@example.com',
    ]));

    $second = app(CreateStaffAction::class)->execute(CreateStaffData::fromArray([
        'school_id' => $schoolB,
        'first_name' => 'Awa',
        'last_name' => 'Kaboré',
        'gender' => 'female',
        'phone_number' => '+22670112299',
        'email' => 'awa@example.com',
    ]));

    expect($first->staff_number)->not->toBe($second->staff_number);
});

test('the same phone number may be used in different schools', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    app(CreateStaffAction::class)->execute(CreateStaffData::fromArray([
        'school_id' => $schoolA,
        'first_name' => 'Adama',
        'last_name' => 'Diallo',
        'gender' => 'male',
        'phone_number' => '+22670112233',
        'email' => 'adama@example.com',
    ]));

    $other = app(CreateStaffAction::class)->execute(CreateStaffData::fromArray([
        'school_id' => $schoolB,
        'first_name' => 'Awa',
        'last_name' => 'Kaboré',
        'gender' => 'female',
        'phone_number' => '+22670112233',
        'email' => 'awa@example.com',
    ]));

    expect($other->school_id)->toBe($schoolB);
});

test('the same phone number in the same school is rejected', function (): void {
    app(CreateStaffAction::class)->execute(CreateStaffData::fromArray([
        'school_id' => $this->schoolId ?? ($this->schoolId = makeTenant()),
        'first_name' => 'Adama',
        'last_name' => 'Diallo',
        'gender' => 'male',
        'phone_number' => '+22670112233',
        'email' => 'adama@example.com',
    ]));

    app(CreateStaffAction::class)->execute(CreateStaffData::fromArray([
        'school_id' => $this->schoolId,
        'first_name' => 'Awa',
        'last_name' => 'Kaboré',
        'gender' => 'female',
        'phone_number' => '+22670112233',
        'email' => 'awa@example.com',
    ]));
})->throws(DuplicateStaffException::class);

test('employment history is never visible across schools', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $staff = app(CreateStaffAction::class)->execute(CreateStaffData::fromArray([
        'school_id' => $schoolA,
        'first_name' => 'Adama',
        'last_name' => 'Diallo',
        'gender' => 'male',
        'phone_number' => '+22670112244',
        'email' => 'adama@example.com',
    ]));

    app(\App\Domains\Staff\Application\Actions\StartEmploymentAction::class)->execute(
        \App\Domains\Staff\Application\DTOs\EmploymentData::fromArray([
            'school_id' => $schoolA,
            'staff_id' => $staff->id,
            'contract_start_date' => now()->subYear()->format('Y-m-d'),
        ]),
    );

    expect(EmploymentHistory::forSchool($schoolA)->count())->toBe(1)
        ->and(EmploymentHistory::forSchool($schoolB)->count())->toBe(0);
});
