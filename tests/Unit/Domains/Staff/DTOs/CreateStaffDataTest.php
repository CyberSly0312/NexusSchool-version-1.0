<?php

declare(strict_types=1);

use App\Domains\Staff\Application\DTOs\CreateStaffData;
use App\Domains\Staff\Domain\Enums\StaffSpecialization;
use App\Domains\Staff\Domain\Enums\StaffStatus;

test('fromArray hydrates a complete data object', function (): void {
    $data = CreateStaffData::fromArray([
        'school_id' => 'school-1',
        'first_name' => 'Adama',
        'last_name' => 'Diallo',
        'gender' => 'male',
        'phone_number' => '+22670112233',
        'specialization' => 'teacher',
    ]);

    expect($data->schoolId)->toBe('school-1')
        ->and($data->firstName)->toBe('Adama')
        ->and($data->specialization)->toBe(StaffSpecialization::Teacher)
        ->and($data->status)->toBe(StaffStatus::Active);
});

test('empty strings normalize to null', function (): void {
    $data = CreateStaffData::fromArray([
        'school_id' => 'school-1',
        'first_name' => 'Adama',
        'last_name' => 'Diallo',
        'phone_number' => '+22670112233',
        'email' => '  ',
        'national_id' => '',
    ]);

    expect($data->email)->toBeNull()
        ->and($data->nationalId)->toBeNull();
});

test('withStaffNumber returns a new immutable instance', function (): void {
    $data = CreateStaffData::fromArray([
        'school_id' => 'school-1',
        'first_name' => 'Adama',
        'last_name' => 'Diallo',
        'phone_number' => '+22670112233',
    ]);

    $updated = $data->withStaffNumber('STF-A1B2C3-0001');

    expect($updated->staffNumber)->toBe('STF-A1B2C3-0001')
        ->and($data->staffNumber)->toBeNull();
});

test('toArray renders persisted column names', function (): void {
    $data = CreateStaffData::fromArray([
        'school_id' => 'school-1',
        'first_name' => 'Adama',
        'last_name' => 'Diallo',
        'phone_number' => '+22670112233',
    ]);

    $array = $data->toArray();

    expect($array['staff_number'])->toBeNull()
        ->and($array['specialization'])->toBe('other')
        ->and($array['status'])->toBe('active');
});
