<?php

declare(strict_types=1);

use App\Domains\Staff\Application\Services\StaffService;
use App\Domains\Staff\Domain\Exceptions\DuplicateStaffException;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->service = app(StaffService::class);
});

test('the service facade creates a staff member end to end', function (): void {
    $staff = $this->service->create($this->schoolId, [
        'first_name' => 'Awa',
        'last_name' => 'Kaboré',
        'gender' => 'female',
        'phone_number' => '+22670112244',
        'specialization' => 'teacher',
    ]);

    expect($staff->exists)->toBeTrue()
        ->and($staff->staff_number)->toMatch('/^STF-/')
        ->and($staff->school_id)->toBe($this->schoolId);
});

test('the service facade exposes the full lifecycle in one place', function (): void {
    $staff = $this->service->create($this->schoolId, [
        'first_name' => 'Issa',
        'last_name' => 'Ouédraogo',
        'gender' => 'male',
        'phone_number' => '+22670112255',
        'specialization' => 'teacher',
    ]);

    $employment = $this->service->startEmployment($this->schoolId, [
        'staff_id' => $staff->id,
        'contract_start_date' => now()->subMonth()->format('Y-m-d'),
    ]);

    expect($employment->isActive())->toBeTrue();

    $this->service->suspend($staff, 'Incident disciplinaire');
    expect($staff->fresh()->status->value)->toBe('suspended');

    $this->service->restore($staff);
    expect($staff->fresh()->status->value)->toBe('active');

    $profile = $this->service->assignTeachingSubjects($staff, [1, 2], ['CM1']);
    expect($profile->teaching_subject_ids)->toBe([1, 2]);
});

test('the service facade rejects duplicates through the action', function (): void {
    $this->service->create($this->schoolId, [
        'first_name' => 'Awa',
        'last_name' => 'Kaboré',
        'gender' => 'female',
        'phone_number' => '+22670112266',
    ]);

    $this->service->create($this->schoolId, [
        'first_name' => 'Bintou',
        'last_name' => 'Kaboré',
        'gender' => 'female',
        'phone_number' => '+22670112266',
    ]);
})->throws(DuplicateStaffException::class);
