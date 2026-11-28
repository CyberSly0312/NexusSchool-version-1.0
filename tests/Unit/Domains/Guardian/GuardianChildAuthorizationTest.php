<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Guardian;

use App\Domains\Guardian\Application\Actions\LinkGuardianAction;
use App\Domains\Guardian\Application\DTOs\GuardianRelationshipRevocationData;
use App\Domains\Guardian\Application\DTOs\GuardianRelationshipVerificationData;
use App\Domains\Guardian\Application\DTOs\RelationshipData;
use App\Domains\Guardian\Application\Services\GuardianChildAccessService;
use App\Domains\Guardian\Application\Services\GuardianRelationshipVerificationService;
use App\Domains\Guardian\Application\Services\GuardianService as ApplicationGuardianService;
use App\Domains\Guardian\Domain\Enums\GuardianAccessDenialReason;
use App\Domains\Guardian\Domain\Enums\GuardianChildOperation;
use App\Domains\Guardian\Domain\Enums\RelationshipPortalAccessStatus;
use App\Domains\Guardian\Domain\Enums\RelationshipVerificationStatus;
use App\Domains\Guardian\Domain\Events\GuardianLinked;
use App\Domains\Guardian\Domain\Exceptions\DuplicateRelationshipException;
use App\Domains\Guardian\Domain\Exceptions\GuardianChildAccessDeniedException;
use App\Domains\Guardian\Domain\Exceptions\GuardianRelationshipVerificationException;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationshipHistory;
use App\Domains\IdentityMatching\Application\DTOs\IdentityMatchRequest;
use App\Domains\IdentityMatching\Application\Services\SchoolScopedIdentityMatcher;
use App\Domains\IdentityMatching\Domain\Enums\IdentityMatchOutcome;
use App\Domains\IdentityMatching\Domain\Enums\MatchableEntityType;
use App\Domains\Role\Policies\SchoolAdminRolePolicy;
use App\Domains\Student\Application\Actions\UpdateStudentAction;
use App\Domains\Student\Application\DTOs\UpdateStudentData;
use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\ValueObjects\GuardianReference;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\Student\Infrastructure\Models\StudentGuardian;
use App\Domains\System\Enums\AuditAction;
use App\Domains\User\Enums\UserStatus;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use ReflectionMethod;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Facades\Tenancy;
use TypeError;

beforeEach(function (): void {
    foreach (['parent', 'school_admin', 'teacher'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }

    foreach (['guardian.link', 'guardian.verify', 'guardian.revoke'] as $permission) {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }
});

afterEach(function (): void {
    if (tenancy()->initialized) {
        Tenancy::end();
    }
});

/**
 * @return array<string, mixed>
 */
function task1113Context(): array
{
    $schoolId = makeTenant('Task 11.3 School', 'active', 'task-11-3.nexusschool.test');
    $otherSchoolId = makeTenant('Task 11.3 Other School', 'active', 'task-11-3-other.nexusschool.test');

    foreach ([$schoolId, $otherSchoolId] as $id) {
        RelationshipType::create([
            'school_id' => $id,
            'code' => 'guardian',
            'label' => 'Tuteur',
            'category' => 'family',
            'is_active' => true,
        ]);
    }

    if (tenancy()->initialized) {
        Tenancy::end();
    }
    tenancy()->initialize($schoolId);

    $parent = User::factory()->create([
        'school_id' => $schoolId,
        'status' => UserStatus::Active,
    ]);
    $parent->assignRole('parent');

    $guardian = Guardian::factory()->create([
        'school_id' => $schoolId,
        'user_id' => $parent->getKey(),
    ]);

    $reviewer = task1113Staff($schoolId, 'school_admin', ['guardian.link', 'guardian.verify', 'guardian.revoke']);
    $unauthorizedStaff = task1113Staff($schoolId, 'teacher', []);
    $crossSchoolReviewer = task1113Staff($otherSchoolId, 'school_admin', ['guardian.link', 'guardian.verify', 'guardian.revoke']);

    $student = Student::factory()->create(['school_id' => $schoolId]);
    $sibling = Student::factory()->create([
        'school_id' => $schoolId,
        'last_name' => $student->last_name,
    ]);

    $context = compact(
        'schoolId',
        'otherSchoolId',
        'parent',
        'guardian',
        'reviewer',
        'unauthorizedStaff',
        'crossSchoolReviewer',
        'student',
        'sibling',
    );

    $context['relationship'] = task1113Link($context, $student, $reviewer);
    Auth::logout();

    return $context;
}

/**
 * @param  list<string>  $permissions
 */
function task1113Staff(string $schoolId, string $roleName, array $permissions): User
{
    $user = User::factory()->create([
        'school_id' => $schoolId,
        'status' => UserStatus::Active,
    ]);
    $user->assignRole($roleName);

    if ($permissions !== []) {
        $user->givePermissionTo($permissions);
    }

    return $user;
}

/**
 * @param  array<string, mixed>  $context
 */
function task1113LinkData(array $context, Student $student, ?Guardian $guardian = null): RelationshipData
{
    $typeId = RelationshipType::query()
        ->forSchool((string) $context['schoolId'])
        ->where('code', 'guardian')
        ->value('id');

    return new RelationshipData(
        schoolId: (string) $context['schoolId'],
        studentId: (int) $student->getKey(),
        guardianId: (int) ($guardian ?? $context['guardian'])->getKey(),
        relationshipTypeId: (int) $typeId,
        relationshipStartDate: now()->toDateString(),
    );
}

function task1113Link(array $context, Student $student, User $actor): StudentGuardianRelationship
{
    test()->actingAs($actor);

    return app(LinkGuardianAction::class)->execute(task1113LinkData($context, $student));
}

function task1113LinkedHistory(int $relationshipId): ?StudentGuardianRelationshipHistory
{
    return StudentGuardianRelationshipHistory::query()
        ->where('student_guardian_id', $relationshipId)
        ->where('event', 'linked')
        ->first();
}

/**
 * @param  array<string, mixed>  $context
 */
function task1113Request(array $context, ?StudentGuardianRelationship $relationship = null, ?User $actor = null): StudentGuardianRelationship
{
    $relationship ??= $context['relationship'];
    test()->actingAs($actor ?? $context['reviewer']);

    return app(GuardianRelationshipVerificationService::class)->request(
        GuardianRelationshipVerificationData::fromRelationship($relationship),
    );
}

/**
 * @param  array<string, mixed>  $context
 */
function task1113Authorize(array $context, ?StudentGuardianRelationship $relationship = null, ?User $actor = null): StudentGuardianRelationship
{
    $relationship ??= $context['relationship'];
    task1113Request($context, $relationship, $actor);

    return app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
        GuardianRelationshipVerificationData::fromRelationship($relationship),
    );
}

/**
 * @param  array<string, mixed>  $context
 */
function task1113Access(array $context, int|string $studentId): StudentGuardianRelationship
{
    test()->actingAs($context['parent']);

    return app(GuardianChildAccessService::class)->assertCanAccess($studentId);
}

/**
 * @param  array<string, mixed>  $context
 */
function task1113ExpectVerificationDenial(array $context, callable $operation, GuardianAccessDenialReason $reason): void
{
    try {
        $operation();
        test()->fail('Guardian relationship operation was not denied.');
    } catch (GuardianRelationshipVerificationException $exception) {
        expect($exception->reason)->toBe($reason);
    }
}

test('a verified guardian can access the explicitly linked student', function (): void {
    $context = task1113Context();
    $relationship = task1113Authorize($context);

    expect($relationship->verification_status)->toBe(RelationshipVerificationStatus::Verified)
        ->and($relationship->portal_access_status)->toBe(RelationshipPortalAccessStatus::Authorized)
        ->and(task1113Access($context, $context['student']->getKey())->getKey())->toBe($relationship->getKey());

    $ids = StudentGuardianRelationship::query()
        ->forSchool($context['schoolId'])
        ->forGuardian($context['guardian']->getKey())
        ->portalAuthorized()
        ->pluck('student_id')
        ->all();

    expect($ids)->toBe([$context['student']->getKey()]);
});

test('one guardian can access two independently approved students and revocation is per relationship', function (): void {
    $context = task1113Context();
    $first = task1113Authorize($context);
    $siblingRelationship = task1113Link($context, $context['sibling'], $context['reviewer']);
    $second = task1113Authorize($context, $siblingRelationship);

    expect(task1113Access($context, $context['student']->getKey())->getKey())->toBe($first->getKey())
        ->and(task1113Access($context, $context['sibling']->getKey())->getKey())->toBe($second->getKey());

    test()->actingAs($context['reviewer']);
    app(GuardianRelationshipVerificationService::class)->revoke(
        GuardianRelationshipRevocationData::fromRelationship($first),
    );

    expect(fn () => task1113Access($context, $context['student']->getKey()))
        ->toThrow(GuardianChildAccessDeniedException::class)
        ->and(task1113Access($context, $context['sibling']->getKey())->getKey())->toBe($second->getKey());
});

test('an unlinked sibling and a missing student cannot be accessed', function (): void {
    $context = task1113Context();
    task1113Authorize($context);

    StudentGuardian::create([
        'school_id' => $context['schoolId'],
        'student_id' => $context['sibling']->getKey(),
        'guardian_id' => null,
        'name' => $context['guardian']->full_name,
        'phone' => $context['guardian']->phone_number,
        'email' => $context['guardian']->email,
        'relationship' => 'parent',
        'is_primary' => true,
    ]);

    foreach ([
        $context['sibling']->getKey() => GuardianAccessDenialReason::RelationshipNotFound,
        999999999 => GuardianAccessDenialReason::StudentNotFound,
    ] as $studentId => $expectedReason) {
        try {
            task1113Access($context, $studentId);
            test()->fail('Unauthorized child access was not blocked.');
        } catch (GuardianChildAccessDeniedException $exception) {
            expect($exception->reason)->toBe($expectedReason);
        }
    }

    expect(StudentGuardianRelationship::query()
        ->forStudent($context['sibling']->getKey())
        ->whereNotNull('guardian_id')
        ->exists())->toBeFalse();
});

test('cross school students and injected relationships cannot authorize access', function (): void {
    $context = task1113Context();
    task1113Authorize($context);

    $foreignStudent = Student::factory()->create(['school_id' => $context['otherSchoolId']]);

    $foreignRelationship = StudentGuardianRelationship::factory()->create([
        'school_id' => $context['otherSchoolId'],
        'student_id' => $context['student']->getKey(),
        'guardian_id' => $context['guardian']->getKey(),
        'relationship_start_date' => now()->toDateString(),
    ]);

    expect(fn () => task1113Access($context, $foreignStudent->getKey()))
        ->toThrow(GuardianChildAccessDeniedException::class)
        ->and(task1113Access($context, $context['student']->getKey())->getKey())->toBe($context['relationship']->getKey());

    test()->actingAs($context['reviewer']);
    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
            GuardianRelationshipVerificationData::fromRelationship($foreignRelationship),
        ),
        GuardianAccessDenialReason::RelationshipNotFound,
    );
});

test('revoked and expired relationships cannot authorize access', function (): void {
    $context = task1113Context();
    $relationship = task1113Authorize($context);

    test()->actingAs($context['reviewer']);
    app(GuardianRelationshipVerificationService::class)->revoke(
        GuardianRelationshipRevocationData::fromRelationship($relationship),
    );

    expect(fn () => task1113Access($context, $context['student']->getKey()))
        ->toThrow(GuardianChildAccessDeniedException::class);

    $relationship->refresh()->update([
        'portal_access_status' => RelationshipPortalAccessStatus::Authorized,
        'portal_revoked_at' => null,
        'relationship_end_date' => now()->subDay()->toDateString(),
    ]);

    try {
        task1113Access($context, $context['student']->getKey());
        test()->fail('Expired relationship was not blocked.');
    } catch (GuardianChildAccessDeniedException $exception) {
        expect($exception->reason)->toBe(GuardianAccessDenialReason::RelationshipExpired);
    }
});

test('exact match and persisted IDs alone do not prove a guardian student relationship', function (): void {
    $context = task1113Context();

    $match = app(SchoolScopedIdentityMatcher::class)->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Guardian,
        schoolId: (string) $context['schoolId'],
        guardianId: $context['guardian']->getKey(),
        userId: $context['parent']->getKey(),
    ));

    expect($match->outcome)->toBe(IdentityMatchOutcome::ExactMatch);

    $untracked = StudentGuardianRelationship::query()->create([
        'school_id' => $context['schoolId'],
        'student_id' => $context['sibling']->getKey(),
        'guardian_id' => $context['guardian']->getKey(),
        'name' => $context['guardian']->full_name,
        'phone' => $context['guardian']->phone_number,
        'email' => $context['guardian']->email,
        'relationship' => 'parent',
        'relationship_start_date' => now()->toDateString(),
    ]);

    task1113ExpectVerificationDenial(
        $context,
        fn () => task1113Authorize($context, $untracked),
        GuardianAccessDenialReason::RelationshipProvenanceMissing,
    );

    expect(fn () => task1113Access($context, $context['sibling']->getKey()))
        ->toThrow(GuardianChildAccessDeniedException::class)
        ->and(StudentGuardianRelationship::query()->forStudent($context['sibling']->getKey())->count())->toBe(1);
});

test('a request never grants portal access before approval', function (): void {
    $context = task1113Context();
    $relationship = task1113Request($context);

    expect($relationship->fresh()->verification_status)->toBe(RelationshipVerificationStatus::Unverified)
        ->and($relationship->fresh()->portal_access_status)->toBe(RelationshipPortalAccessStatus::NotAuthorized)
        ->and(fn () => task1113Access($context, $context['student']->getKey()))
        ->toThrow(GuardianChildAccessDeniedException::class)
        ->and(AuditLog::query()->where('action', AuditAction::GuardianRelationshipRequested->value)->exists())->toBeTrue();
});

test('rejection is persisted and authorized approval follows the request workflow', function (): void {
    $context = task1113Context();
    task1113Request($context);

    test()->actingAs($context['reviewer']);
    $rejected = app(GuardianRelationshipVerificationService::class)->reject(
        GuardianRelationshipVerificationData::fromRelationship($context['relationship']),
    );

    expect($rejected->verification_status)->toBe(RelationshipVerificationStatus::Rejected)
        ->and($rejected->portal_access_status)->toBe(RelationshipPortalAccessStatus::NotAuthorized)
        ->and(fn () => task1113Access($context, $context['student']->getKey()))
        ->toThrow(GuardianChildAccessDeniedException::class)
        ->and(StudentGuardianRelationshipHistory::query()
            ->where('student_guardian_id', $context['relationship']->getKey())
            ->where('event', 'portal_access_rejected')->exists())->toBeTrue();

    $context['relationship']->update(['verification_status' => RelationshipVerificationStatus::Unverified]);
    $approved = task1113Authorize($context);

    expect($approved->verification_status)->toBe(RelationshipVerificationStatus::Verified)
        ->and($approved->portal_access_status)->toBe(RelationshipPortalAccessStatus::Authorized);
});

test('verification is idempotent and duplicate or conflicting rows are rejected', function (): void {
    $context = task1113Context();
    $first = task1113Authorize($context);
    test()->actingAs($context['reviewer']);
    $second = app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
        GuardianRelationshipVerificationData::fromRelationship($context['relationship']),
    );

    expect((int) $second->getKey())->toBe((int) $first->getKey())
        ->and(StudentGuardianRelationship::query()
            ->forSchool($context['schoolId'])
            ->forGuardian($context['guardian']->getKey())
            ->forStudent($context['student']->getKey())
            ->count())->toBe(1);

    StudentGuardianRelationship::factory()->create([
        'school_id' => $context['schoolId'],
        'student_id' => $context['student']->getKey(),
        'guardian_id' => $context['guardian']->getKey(),
        'relationship_start_date' => now()->toDateString(),
    ]);

    test()->actingAs($context['reviewer']);
    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
            GuardianRelationshipVerificationData::fromRelationship($context['relationship']),
        ),
        GuardianAccessDenialReason::RelationshipConflict,
    );
});

test('null and unauthenticated actors cannot verify', function (): void {
    $context = task1113Context();

    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
            GuardianRelationshipVerificationData::fromRelationship($context['relationship']),
        ),
        GuardianAccessDenialReason::ActorNotAuthenticated,
    );
});

test('unauthorized, suspended and cross school actors cannot verify', function (): void {
    $context = task1113Context();

    test()->actingAs($context['unauthorizedStaff']);
    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
            GuardianRelationshipVerificationData::fromRelationship($context['relationship']),
        ),
        GuardianAccessDenialReason::ActorNotAuthorized,
    );

    $context['reviewer']->update(['status' => UserStatus::Suspended]);
    test()->actingAs($context['reviewer']->fresh());
    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
            GuardianRelationshipVerificationData::fromRelationship($context['relationship']),
        ),
        GuardianAccessDenialReason::ActorNotActive,
    );

    test()->actingAs($context['crossSchoolReviewer']);
    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
            GuardianRelationshipVerificationData::fromRelationship($context['relationship']),
        ),
        GuardianAccessDenialReason::ActorSchoolMismatch,
    );
});

test('a parent cannot approve their own relationship even when granted review permission', function (): void {
    $context = task1113Context();
    $context['parent']->givePermissionTo('guardian.verify');
    test()->actingAs($context['parent']);

    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
            GuardianRelationshipVerificationData::fromRelationship($context['relationship']),
        ),
        GuardianAccessDenialReason::ActorNotAuthorized,
    );
});

test('an explicit actor different from the authenticated actor is rejected', function (): void {
    $context = task1113Context();
    test()->actingAs($context['reviewer']);

    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
            GuardianRelationshipVerificationData::fromRelationship($context['relationship']),
            $context['unauthorizedStaff'],
        ),
        GuardianAccessDenialReason::ActorMismatch,
    );
});

test('unauthorized actors cannot reject, approve or revoke a relationship', function (): void {
    $context = task1113Context();
    task1113Request($context);
    test()->actingAs($context['unauthorizedStaff']);

    $data = GuardianRelationshipVerificationData::fromRelationship($context['relationship']);

    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize($data),
        GuardianAccessDenialReason::ActorNotAuthorized,
    );
    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->reject($data),
        GuardianAccessDenialReason::ActorNotAuthorized,
    );

    task1113Authorize($context);
    test()->actingAs($context['unauthorizedStaff']);

    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->revoke(
            GuardianRelationshipRevocationData::fromRelationship($context['relationship']),
        ),
        GuardianAccessDenialReason::ActorNotAuthorized,
    );
});

test('caller supplied identifiers cannot override server resolved targets', function (): void {
    $context = task1113Context();
    $relationshipId = $context['relationship']->getKey();
    $foreignStudent = Student::factory()->create(['school_id' => $context['otherSchoolId']]);

    foreach ([
        ['school_id' => $context['otherSchoolId']],
        ['user_id' => $context['unauthorizedStaff']->getKey()],
        ['guardian_id' => $context['guardian']->getKey() + 999],
        ['student_id' => $foreignStudent->getKey()],
    ] as $override) {
        task1113ExpectVerificationDenial(
            $context,
            fn () => GuardianRelationshipVerificationData::fromArray([
                'relationship_id' => $relationshipId,
                ...$override,
            ]),
            GuardianAccessDenialReason::IdentifierOverrideRejected,
        );
    }

    expect(fn () => GuardianRelationshipRevocationData::fromArray([
        'relationship_id' => $relationshipId,
        'user_id' => $context['parent']->getKey(),
    ]))->toThrow(GuardianRelationshipVerificationException::class);
});

test('missing tenant context fails closed for access and review', function (): void {
    $context = task1113Context();
    task1113Authorize($context);

    Tenancy::end();

    expect(fn () => app(GuardianChildAccessService::class)->assertCanAccess($context['student']->getKey()))
        ->toThrow(GuardianChildAccessDeniedException::class);

    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
            GuardianRelationshipVerificationData::fromRelationship($context['relationship']),
        ),
        GuardianAccessDenialReason::TenantContextMissing,
    );
});

test('audit records identify the reviewer, separate targets and validated tenant school', function (): void {
    $context = task1113Context();
    $relationship = task1113Authorize($context);

    test()->actingAs($context['reviewer']);
    app(GuardianRelationshipVerificationService::class)->revoke(
        GuardianRelationshipRevocationData::fromRelationship($relationship),
    );

    $verified = AuditLog::query()->where('action', AuditAction::GuardianRelationshipVerified->value)->firstOrFail();
    $revoked = AuditLog::query()->where('action', AuditAction::GuardianRelationshipRevoked->value)->firstOrFail();
    $verifiedValues = is_array($verified->new_values)
        ? $verified->new_values
        : json_decode((string) $verified->new_values, true);

    expect((int) $verified->user_id)->toBe((int) $context['reviewer']->getKey())
        ->and((int) $verified->school_id === (int) $context['schoolId'] || (string) $verified->school_id === (string) $context['schoolId'])->toBeTrue()
        ->and($verifiedValues['guardian_id'])->toBe((string) $context['guardian']->getKey())
        ->and($verifiedValues['student_id'])->toBe((string) $context['student']->getKey())
        ->and($verified->action)->not->toBe($revoked->action)
        ->and((int) $revoked->user_id)->toBe((int) $context['reviewer']->getKey());

    $serialized = json_encode(AuditLog::query()->get()->toArray());

    expect($serialized)->toBeString()
        ->and($serialized)->not->toContain($context['parent']->email)
        ->and($serialized)->not->toContain((string) $context['guardian']->phone_number);
});

test('embedded guardian replacement preserves durable authorized relationships', function (): void {
    $context = task1113Context();
    $relationship = task1113Authorize($context);

    StudentGuardian::create([
        'school_id' => $context['schoolId'],
        'student_id' => $context['student']->getKey(),
        'guardian_id' => null,
        'name' => 'Ancien tuteur',
        'phone' => '+225000000000',
        'relationship' => 'parent',
        'is_primary' => true,
    ]);

    app(UpdateStudentAction::class)->execute(
        $context['student'],
        new UpdateStudentData(replaceGuardians: true, guardians: [
            new GuardianReference('Nouveau tuteur', '+225111111111', null, 'parent', true),
            new GuardianReference('Nouveau tuteur', '+225111111111', null, 'parent', false),
        ]),
    );

    expect(StudentGuardianRelationship::query()->whereKey($relationship->getKey())->exists())->toBeTrue()
        ->and($relationship->fresh()->portal_access_status)->toBe(RelationshipPortalAccessStatus::Authorized)
        ->and($context['student']->guardians()->count())->toBe(1)
        ->and($context['student']->guardians()->first()->name)->toBe('Nouveau tuteur')
        ->and(task1113Access($context, $context['student']->getKey())->getKey())->toBe($relationship->getKey());

    test()->actingAs($context['reviewer']);
    app(GuardianRelationshipVerificationService::class)->revoke(
        GuardianRelationshipRevocationData::fromRelationship($relationship),
    );

    expect(fn () => task1113Access($context, $context['student']->getKey()))
        ->toThrow(GuardianChildAccessDeniedException::class);
});

test('the child access service cannot be asked to decide for another user', function (): void {
    $context = task1113Context();
    task1113Authorize($context);
    test()->actingAs($context['parent']);

    expect(fn () => app(GuardianChildAccessService::class)->assertCanAccess(
        $context['unauthorizedStaff']->getKey(),
        $context['student']->getKey(),
    ))->toThrow(TypeError::class)
        ->and(fn () => app(GuardianChildAccessService::class)->assertCanAccess(
            999999999,
        ))->toThrow(GuardianChildAccessDeniedException::class);
});

test('inactive parent accounts and unsupported operations fail closed', function (): void {
    $context = task1113Context();
    task1113Authorize($context);

    expect(fn () => app(GuardianChildAccessService::class)->assertCanAccess(
        $context['student']->getKey(),
        GuardianChildOperation::Edit,
    ))->toThrow(GuardianChildAccessDeniedException::class);

    $context['parent']->update(['status' => UserStatus::Suspended]);
    test()->actingAs($context['parent']->fresh());

    expect(fn () => task1113Access($context, $context['student']->getKey()))
        ->toThrow(GuardianChildAccessDeniedException::class);

    $context['parent']->update(['status' => UserStatus::Active]);
    $context['guardian']->update(['status' => 'archived']);

    expect(fn () => task1113Access($context, $context['student']->getKey()))
        ->toThrow(GuardianChildAccessDeniedException::class);
});

test('school admin role policy and student status remain untouched for parent authorization', function (): void {
    $context = task1113Context();
    $relationship = task1113Authorize($context);

    $context['student']->update(['status' => StudentStatus::Suspended]);

    expect($relationship->fresh()->portal_access_status)->toBe(RelationshipPortalAccessStatus::Authorized)
        ->and(task1113Access($context, $context['student']->getKey())->getKey())->toBe($relationship->getKey());
});

test('link provenance contracts expose no caller supplied actor parameter', function (): void {
    $action = new ReflectionMethod(LinkGuardianAction::class, 'execute');
    $service = new ReflectionMethod(ApplicationGuardianService::class, 'link');
    $event = new ReflectionMethod(GuardianLinked::class, '__construct');

    expect($action->getNumberOfParameters())->toBe(1)
        ->and($action->getParameters()[0]->getName())->toBe('data')
        ->and($service->getNumberOfParameters())->toBe(2)
        ->and(array_map(fn ($parameter): string => $parameter->getName(), $service->getParameters()))
        ->toBe(['schoolId', 'attributes'])
        ->and($event->getNumberOfParameters())->toBe(1)
        ->and($event->getParameters()[0]->getName())->toBe('relationship');
});

test('an unauthenticated actor cannot create a durable link or trusted history', function (): void {
    $context = task1113Context();
    $guardian = Guardian::factory()->create(['school_id' => $context['schoolId']]);
    $data = task1113LinkData($context, $context['sibling'], $guardian);

    Auth::logout();

    task1113ExpectVerificationDenial(
        $context,
        fn () => app(LinkGuardianAction::class)->execute($data),
        GuardianAccessDenialReason::ActorNotAuthenticated,
    );

    expect(StudentGuardianRelationship::query()->forGuardian($guardian->getKey())->exists())->toBeFalse()
        ->and(StudentGuardianRelationshipHistory::query()
            ->where('guardian_id', $guardian->getKey())
            ->where('event', 'linked')
            ->exists())->toBeFalse();
});

test('unauthorized parent suspended and cross school actors cannot create durable links', function (): void {
    $context = task1113Context();

    $suspended = task1113Staff($context['schoolId'], 'school_admin', ['guardian.link']);
    $suspended->update(['status' => UserStatus::Suspended]);

    $parent = $context['parent'];
    $parent->givePermissionTo('guardian.link');

    $cases = [
        'unauthorized staff' => [$context['unauthorizedStaff'], GuardianAccessDenialReason::ActorNotAuthorized],
        'parent' => [$parent->fresh(), GuardianAccessDenialReason::ActorNotAuthorized],
        'suspended actor' => [$suspended->fresh(), GuardianAccessDenialReason::ActorNotActive],
        'cross school actor' => [$context['crossSchoolReviewer'], GuardianAccessDenialReason::ActorSchoolMismatch],
    ];

    foreach ($cases as [$actor, $reason]) {
        $guardian = Guardian::factory()->create(['school_id' => $context['schoolId']]);
        $data = task1113LinkData($context, $context['sibling'], $guardian);

        test()->actingAs($actor);

        task1113ExpectVerificationDenial(
            $context,
            fn () => app(LinkGuardianAction::class)->execute($data),
            $reason,
        );

        expect(StudentGuardianRelationship::query()->forGuardian($guardian->getKey())->exists())->toBeFalse()
            ->and(StudentGuardianRelationshipHistory::query()
                ->where('guardian_id', $guardian->getKey())
                ->where('event', 'linked')
                ->exists())->toBeFalse();
    }
});

test('an authorized same school link records the authenticated actor and signed provenance', function (): void {
    $context = task1113Context();
    $guardian = Guardian::factory()->create(['school_id' => $context['schoolId']]);
    $events = [];

    Event::listen(GuardianLinked::class, function (GuardianLinked $event) use (&$events): void {
        $events[] = $event;
    });

    test()->actingAs($context['reviewer']);
    $relationship = app(LinkGuardianAction::class)->execute(
        task1113LinkData($context, $context['sibling'], $guardian),
    );

    $history = task1113LinkedHistory($relationship->getKey());

    expect($events)->toHaveCount(1)
        ->and((int) $events[0]->actor->getKey())->toBe((int) $context['reviewer']->getKey())
        ->and($history)->not->toBeNull()
        ->and((int) $history->user_id)->toBe((int) $context['reviewer']->getKey())
        ->and($history->user_type)->toBe(User::class)
        ->and((string) $history->school_id)->toBe((string) $context['schoolId'])
        ->and((int) $history->student_id)->toBe((int) $context['sibling']->getKey())
        ->and((int) $history->guardian_id)->toBe((int) $guardian->getKey())
        ->and($relationship->hasTrustedLinkProvenance())->toBeTrue()
        ->and($history->payload['provenance_version'])->toBe(1)
        ->and($history->payload['provenance_signature'])->toBeString()
        ->and($history->payload['provenance_signature'])->not->toBeEmpty();
});

test('a supplied authorized user object cannot replace the authenticated actor', function (): void {
    $context = task1113Context();
    $guardian = Guardian::factory()->create(['school_id' => $context['schoolId']]);
    $data = task1113LinkData($context, $context['sibling'], $guardian);

    test()->actingAs($context['unauthorizedStaff']);
    $method = new ReflectionMethod(LinkGuardianAction::class, 'execute');

    task1113ExpectVerificationDenial(
        $context,
        fn () => $method->invokeArgs(app(LinkGuardianAction::class), [$data, $context['reviewer']]),
        GuardianAccessDenialReason::ActorNotAuthorized,
    );

    expect(StudentGuardianRelationship::query()->forGuardian($guardian->getKey())->exists())->toBeFalse()
        ->and(StudentGuardianRelationshipHistory::query()
            ->where('guardian_id', $guardian->getKey())
            ->where('event', 'linked')
            ->exists())->toBeFalse();
});

test('a supplied actor id cannot influence linked history attribution', function (): void {
    $context = task1113Context();
    $guardian = Guardian::factory()->create(['school_id' => $context['schoolId']]);
    $data = task1113LinkData($context, $context['sibling'], $guardian);

    test()->actingAs($context['reviewer']);
    $method = new ReflectionMethod(LinkGuardianAction::class, 'execute');
    $relationship = $method->invokeArgs(app(LinkGuardianAction::class), [
        $data,
        $context['unauthorizedStaff']->getKey(),
    ]);

    $history = task1113LinkedHistory($relationship->getKey());

    expect($history)->not->toBeNull()
        ->and((int) $history->user_id)->toBe((int) $context['reviewer']->getKey())
        ->and((int) $history->user_id)->not->toBe((int) $context['unauthorizedStaff']->getKey())
        ->and($relationship->hasTrustedLinkProvenance())->toBeTrue();
});

test('the approved super admin path creates trusted provenance without changing the role allowlist', function (): void {
    $context = task1113Context();
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = task1113Staff($context['schoolId'], 'super_admin', []);
    $guardian = Guardian::factory()->create(['school_id' => $context['schoolId']]);

    test()->actingAs($superAdmin);
    $relationship = app(LinkGuardianAction::class)->execute(
        task1113LinkData($context, $context['sibling'], $guardian),
    );

    $history = task1113LinkedHistory($relationship->getKey());

    expect((int) $history->user_id)->toBe((int) $superAdmin->getKey())
        ->and($relationship->hasTrustedLinkProvenance())->toBeTrue()
        ->and(SchoolAdminRolePolicy::ALLOWED_ROLES)->toBe(['teacher', 'secretary', 'accountant']);
});

test('a directly dispatched guardian linked event cannot mint trusted provenance', function (): void {
    $context = task1113Context();

    test()->actingAs($context['reviewer']);
    $relationship = StudentGuardianRelationship::factory()->create([
        'school_id' => $context['schoolId'],
        'student_id' => $context['sibling']->getKey(),
        'guardian_id' => $context['guardian']->getKey(),
        'relationship_start_date' => now()->toDateString(),
    ]);

    task1113ExpectVerificationDenial(
        $context,
        fn () => GuardianLinked::dispatch($relationship),
        GuardianAccessDenialReason::RelationshipProvenanceMissing,
    );

    expect(StudentGuardianRelationshipHistory::query()
        ->where('student_guardian_id', $relationship->getKey())
        ->where('event', 'linked')
        ->exists())->toBeFalse();
});

test('a directly inserted linked history naming an authorized user is not trusted', function (): void {
    $context = task1113Context();

    $forged = StudentGuardianRelationship::factory()->create([
        'school_id' => $context['schoolId'],
        'student_id' => $context['sibling']->getKey(),
        'guardian_id' => $context['guardian']->getKey(),
        'relationship_start_date' => now()->toDateString(),
        'verification_status' => RelationshipVerificationStatus::Verified,
        'portal_access_status' => RelationshipPortalAccessStatus::Authorized,
    ]);

    StudentGuardianRelationshipHistory::create([
        'school_id' => $context['schoolId'],
        'student_guardian_id' => $forged->getKey(),
        'student_id' => $context['sibling']->getKey(),
        'guardian_id' => $context['guardian']->getKey(),
        'event' => 'linked',
        'description' => 'Forged link history',
        'payload' => [
            'provenance_version' => 1,
            'provenance_signature' => 'not-a-valid-attestation',
        ],
        'user_id' => $context['reviewer']->getKey(),
        'user_type' => User::class,
        'occurred_at' => now(),
    ]);

    StudentGuardianRelationshipHistory::create([
        'school_id' => $context['schoolId'],
        'student_guardian_id' => $forged->getKey(),
        'student_id' => $context['sibling']->getKey(),
        'guardian_id' => $context['guardian']->getKey(),
        'event' => 'portal_access_requested',
        'description' => 'Forged request history',
        'payload' => ['reason_code' => 'REQUESTED'],
        'user_id' => $context['reviewer']->getKey(),
        'user_type' => User::class,
        'occurred_at' => now(),
    ]);

    expect($forged->hasTrustedLinkProvenance())->toBeFalse();

    test()->actingAs($context['parent']);
    expect(fn () => app(GuardianChildAccessService::class)->assertCanAccess($context['sibling']->getKey()))
        ->toThrow(GuardianChildAccessDeniedException::class);

    test()->actingAs($context['reviewer']);
    task1113ExpectVerificationDenial(
        $context,
        fn () => app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
            GuardianRelationshipVerificationData::fromRelationship($forged),
        ),
        GuardianAccessDenialReason::RelationshipProvenanceMissing,
    );
});

test('duplicate and cross school link protections remain effective', function (): void {
    $context = task1113Context();

    expect(fn () => task1113Link($context, $context['student'], $context['reviewer']))
        ->toThrow(DuplicateRelationshipException::class);

    $guardian = Guardian::factory()->create(['school_id' => $context['schoolId']]);
    test()->actingAs($context['reviewer']);

    task1113ExpectVerificationDenial(
        $context,
        fn () => app(LinkGuardianAction::class)->execute(RelationshipData::fromArray([
            'school_id' => $context['otherSchoolId'],
            'student_id' => $context['sibling']->getKey(),
            'guardian_id' => $guardian->getKey(),
            'relationship_type_id' => RelationshipType::query()
                ->forSchool($context['schoolId'])
                ->where('code', 'guardian')
                ->value('id'),
        ])),
        GuardianAccessDenialReason::TenantContextMismatch,
    );

    expect(StudentGuardianRelationship::query()->forGuardian($guardian->getKey())->exists())->toBeFalse();
});
