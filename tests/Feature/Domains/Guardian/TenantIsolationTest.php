<?php

declare(strict_types=1);

use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;

test('guardians are isolated per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    Guardian::factory()->count(2)->create(['school_id' => $schoolA]);
    Guardian::factory()->create(['school_id' => $schoolB]);

    expect(Guardian::forSchool($schoolA)->count())->toBe(2)
        ->and(Guardian::forSchool($schoolB)->count())->toBe(1);
});

test('the same guardian phone is allowed across schools', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    Guardian::factory()->create(['school_id' => $schoolA, 'phone_number' => '+237699999990']);
    Guardian::factory()->create(['school_id' => $schoolB, 'phone_number' => '+237699999990']);

    expect(Guardian::forSchool($schoolA)->where('phone_number', '+237699999990')->exists())->toBeTrue()
        ->and(Guardian::forSchool($schoolB)->where('phone_number', '+237699999990')->exists())->toBeTrue();
});

test('the database rejects a duplicate guardian phone within one school', function (): void {
    $school = makeTenant();

    Guardian::factory()->create(['school_id' => $school, 'phone_number' => '+237699999991']);
    Guardian::factory()->create(['school_id' => $school, 'phone_number' => '+237699999991']);
})->throws(Illuminate\Database\QueryException::class);

test('relationship types are materialized per school by the seeder', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    (new \Database\Seeders\Guardian\RelationshipTypeSeeder())->run($schoolA);
    (new \Database\Seeders\Guardian\RelationshipTypeSeeder())->run($schoolB);

    expect(RelationshipType::forSchool($schoolA)->count())->toBe(count(config('nexus-guardian.relationship_types')))
        ->and(RelationshipType::forSchool($schoolB)->count())->toBe(count(config('nexus-guardian.relationship_types')))
        ->and(RelationshipType::forSchool($schoolA)->pluck('code')->all())
            ->toBe(RelationshipType::forSchool($schoolB)->pluck('code')->all());
});

test('re-seeding keeps the existing relationship types', function (): void {
    $school = makeTenant();

    $seeder = new \Database\Seeders\Guardian\RelationshipTypeSeeder();
    $seeder->run($school);
    $seeder->run($school);

    expect(RelationshipType::forSchool($school)->count())->toBe(count(config('nexus-guardian.relationship_types')));
});

test('relationships are isolated per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    StudentGuardianRelationship::factory()->count(2)->create(['school_id' => $schoolA]);
    StudentGuardianRelationship::factory()->create(['school_id' => $schoolB]);

    expect(StudentGuardianRelationship::forSchool($schoolA)->count())->toBe(2)
        ->and(StudentGuardianRelationship::forSchool($schoolB)->count())->toBe(1);
});

test('the relationship scopes filter by role flags', function (): void {
    $school = makeTenant();

    StudentGuardianRelationship::factory()->create(['school_id' => $school]);
    StudentGuardianRelationship::factory()->primary()->create(['school_id' => $school]);
    StudentGuardianRelationship::factory()->emergencyContact()->create(['school_id' => $school]);
    StudentGuardianRelationship::factory()->authorizedPickup()->create(['school_id' => $school]);

    expect(StudentGuardianRelationship::forSchool($school)->count())->toBe(4)
        ->and(StudentGuardianRelationship::forSchool($school)->primary()->count())->toBe(1)
        ->and(StudentGuardianRelationship::forSchool($school)->emergencyContacts()->count())->toBe(1)
        ->and(StudentGuardianRelationship::forSchool($school)->authorizedPickups()->count())->toBe(1);
});

test('soft-deleted relationships are excluded by default', function (): void {
    $school = makeTenant();

    $relationship = StudentGuardianRelationship::factory()->create(['school_id' => $school]);
    $relationship->delete();

    expect(StudentGuardianRelationship::forSchool($school)->count())->toBe(0)
        ->and(StudentGuardianRelationship::forSchool($school)->withTrashed()->count())->toBe(1);
});
