<?php

declare(strict_types=1);

use App\Domains\Guardian\Application\DTOs\GuardianData;
use App\Domains\Guardian\Application\DTOs\RelationshipData;
use App\Domains\Guardian\Domain\Enums\GuardianStatus;
use App\Domains\Guardian\Domain\Enums\PreferredContactMethod;

test('guardian data round-trips through fromArray and toArray', function (): void {
    $data = GuardianData::fromArray([
        'school_id' => 'school-1',
        'first_name' => 'Marie',
        'last_name' => 'Dupont',
        'middle_name' => ' Claire ',
        'gender' => 'female',
        'email' => 'marie@example.com',
        'phone_number' => '+237690123456',
        'preferred_contact_method' => 'whatsapp',
        'notes' => '  ',
    ]);

    expect($data->schoolId)->toBe('school-1')
        ->and($data->middleName)->toBe('Claire')
        ->and($data->notes)->toBeNull()
        ->and($data->preferredContactMethod)->toBe(PreferredContactMethod::WhatsApp)
        ->and($data->status)->toBe(GuardianStatus::Active)
        ->and($data->relationshipType)->toBe('guardian');
});

test('guardian data defaults are applied when fields are missing', function (): void {
    $data = GuardianData::fromArray([
        'school_id' => 'school-1',
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
    ]);

    expect($data->guardianNumber)->toBeNull()
        ->and($data->preferredContactMethod)->toBe(PreferredContactMethod::Phone)
        ->and($data->status)->toBe(GuardianStatus::Active);
});

test('guardian data with guardian number keeps it', function (): void {
    $data = GuardianData::fromArray([
        'school_id' => 'school-1',
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'guardian_number' => 'GUA-ABCDEF-0001',
    ]);

    expect($data->guardianNumber)->toBe('GUA-ABCDEF-0001');
});

test('guardian data with a blank guardian number drops it', function (): void {
    $data = GuardianData::fromArray([
        'school_id' => 'school-1',
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'guardian_number' => '   ',
    ]);

    expect($data->guardianNumber)->toBeNull();
});

test('withGuardianNumber overrides only the number', function (): void {
    $data = GuardianData::fromArray([
        'school_id' => 'school-1',
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
    ]);

    $assigned = $data->withGuardianNumber('GUA-ABCDEF-0002');

    expect($assigned->guardianNumber)->toBe('GUA-ABCDEF-0002')
        ->and($assigned->firstName)->toBe('Jean')
        ->and($assigned->toArray()['guardian_number'])->toBe('GUA-ABCDEF-0002');
});

test('relationship data round-trips through fromArray and toArray', function (): void {
    $data = RelationshipData::fromArray([
        'school_id' => 'school-1',
        'student_id' => '10',
        'guardian_id' => '20',
        'relationship_type_id' => '3',
        'is_primary' => true,
        'is_emergency_contact' => true,
        'emergency_contact_priority' => '1',
        'pickup_valid_from' => '2026-09-01',
        'pickup_valid_until' => '2027-06-30',
        'notes' => '  ',
    ]);

    expect($data->studentId)->toBe(10)
        ->and($data->guardianId)->toBe(20)
        ->and($data->relationshipTypeId)->toBe(3)
        ->and($data->isPrimary)->toBeTrue()
        ->and($data->isEmergencyContact)->toBeTrue()
        ->and($data->emergencyContactPriority)->toBe(1)
        ->and($data->pickupValidFrom)->toBe('2026-09-01')
        ->and($data->notes)->toBeNull()
        ->and($data->isAuthorizedPickup)->toBeFalse();
});
