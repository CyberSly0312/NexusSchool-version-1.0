<?php

declare(strict_types=1);

use App\Domains\Guardian\Domain\Rules\GuardianRules;
use App\Domains\Guardian\Domain\Rules\RelationshipRules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

test('guardian create rules accept a minimal valid payload', function (): void {
    $validator = Validator::make(
        [
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
        ],
        GuardianRules::create(),
    );

    expect($validator->fails())->toBeFalse();
});

test('guardian create rules reject a missing last name', function (): void {
    $validator = Validator::make(['first_name' => 'Marie'], GuardianRules::create());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('last_name'))->toBeTrue();
});

test('guardian create rules reject an unknown relationship type code', function (): void {
    $validator = Validator::make(
        ['relationship_type' => 'cousin'],
        GuardianRules::create(),
    );

    expect($validator->fails())->toBeTrue();
});

test('guardian create rules accept every catalog relationship type code', function (): void {
    foreach (config('nexus-guardian.relationship_types', []) as $definition) {
        $validator = Validator::make(
            [
                'first_name' => 'Marie',
                'last_name' => 'Dupont',
                'relationship_type' => $definition['code'],
            ],
            GuardianRules::create(),
        );

        expect($validator->fails())->toBeFalse()
            ->and($definition['code'])->toBeString();
    }
});

test('guardian update rules are partial by design', function (): void {
    $validator = Validator::make(['occupation' => 'Ingénieure'], GuardianRules::update('school-1', 1));

    expect($validator->fails())->toBeFalse();
});

test('guardian validate returns the validated payload', function (): void {
    $validated = GuardianRules::validate([
        'first_name' => 'Marie',
        'last_name' => 'Dupont',
    ]);

    expect($validated['first_name'])->toBe('Marie')
        ->and($validated['last_name'])->toBe('Dupont');
});

test('guardian validate throws on invalid data', function (): void {
    GuardianRules::validate(['first_name' => 'Marie']);
})->throws(ValidationException::class);

test('relationship rules accept a minimal valid payload', function (): void {
    $schoolId = makeTenant();
    $student = App\Domains\Student\Infrastructure\Models\Student::factory()->create(['school_id' => $schoolId]);
    $guardian = App\Domains\Guardian\Infrastructure\Models\Guardian::factory()->create(['school_id' => $schoolId]);

    $validator = Validator::make(
        [
            'school_id' => $schoolId,
            'student_id' => $student->id,
            'guardian_id' => $guardian->id,
            'relationship_type_id' => 3,
        ],
        RelationshipRules::create(),
    );

    expect($validator->fails())->toBeFalse();
});

test('relationship rules reject a negative emergency priority', function (): void {
    $schoolId = makeTenant();
    $student = App\Domains\Student\Infrastructure\Models\Student::factory()->create(['school_id' => $schoolId]);
    $guardian = App\Domains\Guardian\Infrastructure\Models\Guardian::factory()->create(['school_id' => $schoolId]);

    $validator = Validator::make(
        [
            'school_id' => $schoolId,
            'student_id' => $student->id,
            'guardian_id' => $guardian->id,
            'relationship_type_id' => 3,
            'emergency_contact_priority' => -1,
        ],
        RelationshipRules::create(),
    );

    expect($validator->fails())->toBeTrue();
});

test('relationship validate throws on invalid data', function (): void {
    RelationshipRules::validate(['school_id' => 'school-1']);
})->throws(ValidationException::class);
