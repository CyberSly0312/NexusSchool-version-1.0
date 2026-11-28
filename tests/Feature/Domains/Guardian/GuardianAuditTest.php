<?php

declare(strict_types=1);

use App\Domains\Guardian\Application\Actions\AssignEmergencyContactAction;
use App\Domains\Guardian\Application\Actions\AssignPickupAuthorizationAction;
use App\Domains\Guardian\Application\Actions\AssignPrimaryGuardianAction;
use App\Domains\Guardian\Application\Actions\CreateGuardianAction;
use App\Domains\Guardian\Application\Actions\LinkGuardianAction;
use App\Domains\Guardian\Application\Actions\UnlinkGuardianAction;
use App\Domains\Guardian\Application\Actions\UpdateGuardianAction;
use App\Domains\Guardian\Application\DTOs\GuardianData;
use App\Domains\Guardian\Application\DTOs\RelationshipData;
use App\Domains\Guardian\Domain\ValueObjects\PickupWindow;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationshipHistory;
use App\Domains\School\Models\School;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\User\Enums\UserStatus;
use App\Models\AuditLog;
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
    $this->relationshipType = RelationshipType::factory()->create(['school_id' => $this->schoolId]);

    Permission::firstOrCreate(['name' => 'guardian.link', 'guard_name' => 'web']);
    $this->actor = User::factory()->create([
        'school_id' => $this->schoolId,
        'status' => UserStatus::Active,
    ]);
    $this->actor->givePermissionTo('guardian.link');
});

afterEach(function (): void {
    if (tenancy()->initialized) {
        Tenancy::end();
    }
});

function createAuditedGuardian(object $context): Guardian
{
    return app(CreateGuardianAction::class)->execute(GuardianData::fromArray([
        'school_id' => $context->schoolId,
        'first_name' => 'Marie',
        'last_name' => 'Dupont',
        'gender' => 'female',
        'phone_number' => '+237690123456',
        'email' => 'marie@example.com',
    ]), $context->actor);
}

function linkAuditedRelationship(object $context, Guardian $guardian): StudentGuardianRelationship
{
    $context->actingAs($context->actor);

    return app(LinkGuardianAction::class)->execute(RelationshipData::fromArray([
        'school_id' => $context->schoolId,
        'student_id' => $context->student->id,
        'guardian_id' => $guardian->id,
        'relationship_type_id' => $context->relationshipType->id,
        'relationship_start_date' => now()->toDateString(),
    ]));
}

test('registration writes an audit log entry', function (): void {
    $guardian = createAuditedGuardian($this);

    $log = AuditLog::where('auditable_id', $guardian->id)
        ->where('auditable_type', Guardian::class)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->action)->toBe('created')
        ->and($log->school_id)->toBe($this->schoolId)
        ->and($log->user_id)->toBe($this->actor->id);
});

test('guardian updates are audited', function (): void {
    $guardian = createAuditedGuardian($this);

    app(UpdateGuardianAction::class)->execute($guardian, ['occupation' => 'Ingénieure'], $this->actor);

    $actions = AuditLog::where('auditable_id', $guardian->id)
        ->where('auditable_type', Guardian::class)
        ->orderBy('id')
        ->pluck('action')
        ->all();

    expect($actions)->toBe(['created', 'updated']);
});

test('the full relationship lifecycle is audited and traced', function (): void {
    $guardian = createAuditedGuardian($this);
    $relationship = linkAuditedRelationship($this, $guardian);

    app(AssignPrimaryGuardianAction::class)->execute($relationship, $this->actor);
    app(AssignEmergencyContactAction::class)->execute($relationship->refresh(), priority: 1, actor: $this->actor);
    app(AssignPickupAuthorizationAction::class)->execute(
        $relationship->refresh(),
        window: new PickupWindow(validFrom: '2026-09-01', validUntil: '2027-06-30'),
        actor: $this->actor,
    );
    app(UnlinkGuardianAction::class)->execute($relationship->refresh(), 'Transfert scolaire', $this->actor);

    $events = StudentGuardianRelationshipHistory::where('student_guardian_id', $relationship->id)
        ->orderBy('id')
        ->pluck('event')
        ->all();

    expect($events)->toBe(['linked', 'primary_assigned', 'emergency_assigned', 'pickup_granted', 'unlinked']);

    $unlinkLog = AuditLog::where('auditable_id', $relationship->id)
        ->where('auditable_type', StudentGuardianRelationship::class)
        ->where('action', 'unlinked')
        ->first();

    expect($unlinkLog)->not->toBeNull()
        ->and($unlinkLog->description)->toContain('Transfert scolaire');
});

test('revoking roles appends the revoke events', function (): void {
    $guardian = createAuditedGuardian($this);
    $relationship = linkAuditedRelationship($this, $guardian);

    app(AssignEmergencyContactAction::class)->execute($relationship, priority: 1, actor: $this->actor);
    app(AssignEmergencyContactAction::class)->execute($relationship->refresh(), grant: false, actor: $this->actor);
    app(AssignPickupAuthorizationAction::class)->execute(
        $relationship->refresh(),
        grant: false,
        window: new PickupWindow(),
        actor: $this->actor,
    );

    $events = StudentGuardianRelationshipHistory::where('student_guardian_id', $relationship->id)
        ->orderBy('id')
        ->pluck('event')
        ->all();

    expect($events)->toBe(['linked', 'emergency_assigned', 'emergency_revoked', 'pickup_revoked']);
});

test('audit logs and history are isolated per school', function (): void {
    $otherSchool = makeTenant();

    $guardian = createAuditedGuardian($this);
    linkAuditedRelationship($this, $guardian);

    expect(AuditLog::forSchool($this->schoolId)->count())->toBeGreaterThan(0)
        ->and(AuditLog::forSchool($otherSchool)->count())->toBe(0)
        ->and(StudentGuardianRelationshipHistory::forSchool($this->schoolId)->count())->toBe(1)
        ->and(StudentGuardianRelationshipHistory::forSchool($otherSchool)->count())->toBe(0);
});
