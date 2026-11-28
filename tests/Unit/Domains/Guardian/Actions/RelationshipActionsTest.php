<?php

declare(strict_types=1);

use App\Domains\Guardian\Application\Actions\AssignEmergencyContactAction;
use App\Domains\Guardian\Application\Actions\AssignPickupAuthorizationAction;
use App\Domains\Guardian\Application\Actions\AssignPrimaryGuardianAction;
use App\Domains\Guardian\Application\Actions\LinkGuardianAction;
use App\Domains\Guardian\Application\Actions\UnlinkGuardianAction;
use App\Domains\Guardian\Application\DTOs\RelationshipData;
use App\Domains\Guardian\Domain\Exceptions\DuplicateRelationshipException;
use App\Domains\Guardian\Domain\Exceptions\RelationshipStateException;
use App\Domains\Guardian\Domain\ValueObjects\PickupWindow;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\School\Models\School;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\User\Enums\UserStatus;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Stancl\Tenancy\Facades\Tenancy;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    if (tenancy()->initialized) {
        Tenancy::end();
    }
    Tenancy::initialize(School::query()->findOrFail($this->schoolId));

    $this->student = Student::factory()->create(['school_id' => $this->schoolId]);
    $this->guardian = Guardian::factory()->create(['school_id' => $this->schoolId]);
    $this->guardianTwo = Guardian::factory()->create([
        'school_id' => $this->schoolId,
        'guardian_number' => 'GUA-SECOND-0001',
        'phone_number' => '+237699999991',
    ]);
    $this->relationshipType = RelationshipType::factory()->create(['school_id' => $this->schoolId]);

    Permission::firstOrCreate(['name' => 'guardian.link', 'guard_name' => 'web']);
    $this->actor = User::factory()->create([
        'school_id' => $this->schoolId,
        'status' => UserStatus::Active,
    ]);
    $this->actor->givePermissionTo('guardian.link');
    $this->actingAs($this->actor);

    $this->link = app(LinkGuardianAction::class);
});

afterEach(function (): void {
    if (tenancy()->initialized) {
        Tenancy::end();
    }
});

function relationshipData(object $context, int $guardianId, array $overrides = []): RelationshipData
{
    return RelationshipData::fromArray(array_merge([
        'school_id' => $context->schoolId,
        'student_id' => $context->student->id,
        'guardian_id' => $guardianId,
        'relationship_type_id' => $context->relationshipType->id,
        'relationship_start_date' => now()->toDateString(),
    ], $overrides));
}

test('linking a guardian creates a first-class relationship', function (): void {
    $relationship = $this->link->execute(relationshipData($this, $this->guardian->id));

    expect($relationship->exists)->toBeTrue()
        ->and($relationship->student_id)->toBe($this->student->id)
        ->and($relationship->guardian_id)->toBe($this->guardian->id)
        ->and($relationship->relationship_type_id)->toBe($this->relationshipType->id)
        ->and($relationship->is_primary)->toBeFalse()
        ->and($relationship->name)->toBe($this->guardian->full_name);
});

test('linking a primary guardian clears previous primaries for the student', function (): void {
    app(LinkGuardianAction::class)->execute(relationshipData($this, $this->guardian->id, ['is_primary' => true]));

    $relationship = app(LinkGuardianAction::class)->execute(
        relationshipData($this, $this->guardianTwo->id, ['is_primary' => true]),
    );

    expect($relationship->is_primary)->toBeTrue()
        ->and(StudentGuardianRelationship::forSchool($this->schoolId)->primary()->count())->toBe(1);
});

test('linking the same guardian twice is rejected', function (): void {
    app(LinkGuardianAction::class)->execute(relationshipData($this, $this->guardian->id));

    app(LinkGuardianAction::class)->execute(relationshipData($this, $this->guardian->id));
})->throws(DuplicateRelationshipException::class);

test('linking a guardian from another school is rejected', function (): void {
    $otherSchool = makeTenant();
    $foreignGuardian = Guardian::factory()->create(['school_id' => $otherSchool]);

    app(LinkGuardianAction::class)->execute(relationshipData($this, $foreignGuardian->id));
})->throws(RelationshipStateException::class);

test('linking with a relationship type from another school is rejected', function (): void {
    $otherSchool = makeTenant();
    $foreignType = RelationshipType::factory()->create(['school_id' => $otherSchool]);

    app(LinkGuardianAction::class)->execute(RelationshipData::fromArray([
        'school_id' => $this->schoolId,
        'student_id' => $this->student->id,
        'guardian_id' => $this->guardian->id,
        'relationship_type_id' => $foreignType->id,
    ]));
})->throws(RelationshipStateException::class);

test('unlinking a relationship ends it and clears the roles', function (): void {
    $relationship = app(LinkGuardianAction::class)->execute(relationshipData($this, $this->guardian->id, [
        'is_primary' => true,
        'is_emergency_contact' => true,
        'is_authorized_pickup' => true,
    ]));

    app(UnlinkGuardianAction::class)->execute($relationship);

    expect($relationship->trashed())->toBeTrue()
        ->and($relationship->relationship_end_date)->not->toBeNull()
        ->and($relationship->is_primary)->toBeFalse()
        ->and($relationship->is_emergency_contact)->toBeFalse()
        ->and($relationship->is_authorized_pickup)->toBeFalse();
});

test('unlinking frees the guardian-student pair for a future link', function (): void {
    $relationship = app(LinkGuardianAction::class)->execute(relationshipData($this, $this->guardian->id));

    app(UnlinkGuardianAction::class)->execute($relationship);

    $relinked = app(LinkGuardianAction::class)->execute(relationshipData($this, $this->guardian->id));

    expect($relinked->exists)->toBeTrue();
});

test('assigning a primary guardian demotes the previous one', function (): void {
    $first = app(LinkGuardianAction::class)->execute(relationshipData($this, $this->guardian->id, ['is_primary' => true]));
    $second = app(LinkGuardianAction::class)->execute(relationshipData($this, $this->guardianTwo->id));

    app(AssignPrimaryGuardianAction::class)->execute($second);

    expect($first->refresh()->is_primary)->toBeFalse()
        ->and($second->refresh()->is_primary)->toBeTrue();
});

test('assigning a primary guardian on an unlinked relationship is rejected', function (): void {
    $relationship = app(LinkGuardianAction::class)->execute(relationshipData($this, $this->guardian->id));

    app(UnlinkGuardianAction::class)->execute($relationship);

    app(AssignPrimaryGuardianAction::class)->execute($relationship);
})->throws(RelationshipStateException::class);

test('emergency contact assignment grants and revokes the role', function (): void {
    $relationship = app(LinkGuardianAction::class)->execute(relationshipData($this, $this->guardian->id));

    app(AssignEmergencyContactAction::class)->execute($relationship, priority: 1);
    expect($relationship->refresh()->is_emergency_contact)->toBeTrue()
        ->and($relationship->emergency_contact_priority)->toBe(1);

    app(AssignEmergencyContactAction::class)->execute($relationship->refresh(), grant: false);
    expect($relationship->refresh()->is_emergency_contact)->toBeFalse()
        ->and($relationship->emergency_contact_priority)->toBeNull();
});

test('pickup authorization stores the validity window', function (): void {
    $relationship = app(LinkGuardianAction::class)->execute(relationshipData($this, $this->guardian->id));

    app(AssignPickupAuthorizationAction::class)->execute(
        $relationship,
        window: new PickupWindow(validFrom: '2026-09-01', validUntil: '2027-06-30'),
    );

    expect($relationship->refresh()->is_authorized_pickup)->toBeTrue()
        ->and($relationship->pickup_valid_from->toDateString())->toBe('2026-09-01')
        ->and($relationship->isPickupValid('2026-10-01'))->toBeTrue()
        ->and($relationship->isPickupValid('2027-09-01'))->toBeFalse();
});
