<?php

declare(strict_types=1);

use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationshipHistory;
use App\Domains\IdentityMatching\Application\DTOs\IdentityMatchRequest;
use App\Domains\IdentityMatching\Application\Services\SchoolScopedIdentityMatcher;
use App\Domains\IdentityMatching\Domain\Enums\IdentityEvidenceType;
use App\Domains\IdentityMatching\Domain\Enums\IdentityMatchOutcome;
use App\Domains\IdentityMatching\Domain\Enums\IdentityMatchReasonCode;
use App\Domains\IdentityMatching\Domain\Enums\MatchableEntityType;
use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\Student\Infrastructure\Models\StudentGuardian;
use App\Domains\User\Enums\UserStatus;
use App\Models\User;
use Spatie\Permission\Models\Role;

function task111InitializeTenant(string $schoolId): void
{
    tenancy()->initialize($schoolId);
}

afterEach(function (): void {
    if (tenancy()->initialized) {
        tenancy()->end();
    }
});

/**
 * @param  array<string, mixed>  $attributes
 */
function task111ParentUser(string $schoolId, array $attributes = []): User
{
    Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);

    /** @var User $user */
    $user = User::factory()->create(array_merge([
        'school_id' => $schoolId,
        'status' => UserStatus::Active,
    ], $attributes));

    $user->assignRole('parent');

    return $user;
}

function task111DurableRelationship(
    string $schoolId,
    Student $student,
    Guardian $guardian,
    bool $primary = false,
): StudentGuardianRelationship {
    return StudentGuardianRelationship::create([
        'school_id' => $schoolId,
        'student_id' => $student->getKey(),
        'guardian_id' => $guardian->getKey(),
        'name' => $guardian->full_name,
        'phone' => $guardian->phone_number,
        'email' => $guardian->email,
        'relationship' => 'guardian',
        'is_primary' => $primary,
        'is_emergency_contact' => false,
        'is_authorized_pickup' => false,
        'is_financial_responsible' => true,
        'is_notification_recipient' => true,
        'is_medical_contact' => false,
    ]);
}

function task111Matcher(): SchoolScopedIdentityMatcher
{
    return new SchoolScopedIdentityMatcher();
}

test('an exact same-school durable identifier produces a reusable exact match', function (): void {
    $schoolId = makeTenant('Matching School');
    task111InitializeTenant($schoolId);

    $student = Student::factory()->create([
        'school_id' => $schoolId,
        'student_number' => 'STU-111',
        'status' => StudentStatus::Active,
    ]);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Student,
        schoolId: $schoolId,
        studentNumber: 'STU-111',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::ExactMatch)
        ->and($result->candidateId)->toBe($student->getKey())
        ->and($result->candidateFound)->toBeTrue()
        ->and($result->evidence)->toContain(IdentityEvidenceType::DurableSchoolIdentifier)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::DurableIdentifierMatch)
        ->and($result->automaticReusePermitted)->toBeTrue();
});

test('an exact email with same-school durable corroboration permits account reuse', function (): void {
    $schoolId = makeTenant('Corroborated School');
    task111InitializeTenant($schoolId);

    $parent = task111ParentUser($schoolId, ['email' => 'parent.corroborated@example.test']);
    $student = Student::factory()->create(['school_id' => $schoolId]);
    $guardian = Guardian::factory()->create([
        'school_id' => $schoolId,
        'user_id' => $parent->getKey(),
        'email' => $parent->email,
    ]);
    task111DurableRelationship($schoolId, $student, $guardian);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::User,
        schoolId: $schoolId,
        email: 'parent.corroborated@example.test',
        guardianId: $guardian->getKey(),
        studentId: $student->getKey(),
        sameSchoolCorroboration: true,
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::ExactMatch)
        ->and($result->candidateId)->toBe($parent->getKey())
        ->and($result->evidence)->toContain(
            IdentityEvidenceType::ExactNormalizedEmail,
            IdentityEvidenceType::SameSchoolCorroboration,
        )
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::ExactEmailWithCorroboration)
        ->and($result->automaticReusePermitted)->toBeTrue();
});

test('an exact email without corroboration is only a possible match', function (): void {
    $schoolId = makeTenant('Uncorroborated School');
    task111InitializeTenant($schoolId);

    $parent = task111ParentUser($schoolId, ['email' => 'parent.uncorroborated@example.test']);
    $student = Student::factory()->create(['school_id' => $schoolId]);
    $guardian = Guardian::factory()->create([
        'school_id' => $schoolId,
        'user_id' => $parent->getKey(),
        'email' => $parent->email,
    ]);
    task111DurableRelationship($schoolId, $student, $guardian);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::User,
        schoolId: $schoolId,
        email: 'parent.uncorroborated@example.test',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::PossibleMatch)
        ->and($result->candidateId)->toBe($parent->getKey())
        ->and($result->evidence)->toContain(IdentityEvidenceType::ExactNormalizedEmail)
        ->and($result->evidence)->not->toContain(IdentityEvidenceType::SameSchoolCorroboration)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::ExactEmailWithoutCorroboration)
        ->and($result->automaticReusePermitted)->toBeFalse();
});

test('name-only similarity never produces an exact match', function (): void {
    $schoolId = makeTenant('Name School');
    task111InitializeTenant($schoolId);

    Student::factory()->create([
        'school_id' => $schoolId,
        'first_name' => 'Amina',
        'last_name' => 'Diallo',
    ]);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Student,
        schoolId: $schoolId,
        firstName: 'Amina',
        lastName: 'Diallo',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::PossibleMatch)
        ->and($result->evidence)->toContain(IdentityEvidenceType::NameSimilarity)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::NameOnly)
        ->and($result->automaticReusePermitted)->toBeFalse();
});

test('phone-only similarity never produces an exact match', function (): void {
    $schoolId = makeTenant('Phone School');
    task111InitializeTenant($schoolId);

    task111ParentUser($schoolId, ['phone' => '+221 77 123 45 67']);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::User,
        schoolId: $schoolId,
        phone: '+221771234567',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::PossibleMatch)
        ->and($result->evidence)->toContain(IdentityEvidenceType::PhoneSimilarity)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::PhoneOnly)
        ->and($result->automaticReusePermitted)->toBeFalse();
});

test('multiple plausible candidates produce an ambiguous or conflict result', function (): void {
    $schoolId = makeTenant('Ambiguous School');
    task111InitializeTenant($schoolId);

    Student::factory()->create([
        'school_id' => $schoolId,
        'phone_number' => '+221 77 000 00 01',
        'first_name' => 'Fatou',
        'last_name' => 'Sow',
    ]);
    Student::factory()->create([
        'school_id' => $schoolId,
        'phone_number' => '+221 77 000 00 01',
        'first_name' => 'Fatou',
        'last_name' => 'Sow',
    ]);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Student,
        schoolId: $schoolId,
        phone: '+221770000001',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::MultipleCandidates)
        ->and($result->candidateId)->toBeNull()
        ->and($result->candidateFound)->toBeFalse()
        ->and($result->automaticReusePermitted)->toBeFalse();
});

test('conflicting durable evidence produces an ambiguous or conflict result', function (): void {
    $schoolId = makeTenant('Conflict School');
    task111InitializeTenant($schoolId);

    Student::factory()->create([
        'school_id' => $schoolId,
        'student_number' => 'STU-REAL',
        'email' => 'real.student@example.test',
    ]);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Student,
        schoolId: $schoolId,
        email: 'real.student@example.test',
        studentNumber: 'STU-OTHER',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::ConflictingEvidence)
        ->and($result->evidence)->toContain(IdentityEvidenceType::ConflictingIdentity)
        ->and($result->automaticReusePermitted)->toBeFalse();
});

test('a candidate in another school cannot be reused', function (): void {
    $schoolA = makeTenant('School A');
    $schoolB = makeTenant('School B');
    task111InitializeTenant($schoolA);

    $foreignParent = task111ParentUser($schoolB, ['phone' => '+221 77 555 55 55']);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::User,
        schoolId: $schoolA,
        phone: '+2217755555555',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::PossibleMatch)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::NoCandidate)
        ->and($result->candidateId)->toBeNull()
        ->and($result->candidateFound)->toBeFalse()
        ->and($result->automaticReusePermitted)->toBeFalse()
        ->and(json_encode($result, JSON_THROW_ON_ERROR))->not->toContain($foreignParent->email);
});

test('a globally unique email owned by another school remains a conflict', function (): void {
    $schoolA = makeTenant('Local School');
    $schoolB = makeTenant('Foreign School');
    task111InitializeTenant($schoolA);

    task111ParentUser($schoolB, ['email' => 'cross.school@example.test']);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::User,
        schoolId: $schoolA,
        email: 'cross.school@example.test',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::CrossSchoolEmailConflict)
        ->and($result->evidence)->toContain(IdentityEvidenceType::CrossSchoolConstraint)
        ->and($result->candidateId)->toBeNull()
        ->and($result->automaticReusePermitted)->toBeFalse();
});

test('a missing tenant context fails closed', function (): void {
    $schoolId = makeTenant('No Tenant Context School');

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::User,
        schoolId: $schoolId,
        email: 'missing.context@example.test',
    ));

    expect(tenant())->toBeNull()
        ->and($result->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::TenantContextMissing)
        ->and($result->candidateId)->toBeNull()
        ->and($result->automaticReusePermitted)->toBeFalse();
});

test('an inconsistent or inactive school context fails closed', function (): void {
    $schoolA = makeTenant('Context School A');
    task111InitializeTenant($schoolA);
    $schoolB = makeTenant('Context School B');

    $mismatch = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Student,
        schoolId: $schoolB,
        studentNumber: 'STU-CONTEXT',
    ));

    expect($mismatch->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($mismatch->reasonCodes)->toContain(IdentityMatchReasonCode::TenantContextMismatch)
        ->and($mismatch->automaticReusePermitted)->toBeFalse();

    tenancy()->end();
    $suspended = makeTenant('Suspended Context School', 'suspended');
    task111InitializeTenant($suspended);

    $inactive = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Student,
        schoolId: $suspended,
        studentNumber: 'STU-CONTEXT',
    ));

    expect($inactive->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($inactive->reasonCodes)->toContain(IdentityMatchReasonCode::TenantSchoolInactive)
        ->and($inactive->automaticReusePermitted)->toBeFalse();
});

test('a soft-deleted user email is a conflict and cannot be reused', function (): void {
    $schoolId = makeTenant('Soft Deleted School');
    task111InitializeTenant($schoolId);

    $parent = task111ParentUser($schoolId, ['email' => 'deleted.parent@example.test']);
    $parent->delete();

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::User,
        schoolId: $schoolId,
        email: 'deleted.parent@example.test',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::SoftDeletedCandidate)
        ->and($result->evidence)->toContain(IdentityEvidenceType::SoftDeletedRecord)
        ->and($result->automaticReusePermitted)->toBeFalse();
});

test('embedded guardian references are possible evidence and are never linked', function (): void {
    $schoolId = makeTenant('Embedded Reference School');
    task111InitializeTenant($schoolId);

    $student = Student::factory()->create(['school_id' => $schoolId]);
    $embedded = StudentGuardian::create([
        'school_id' => $schoolId,
        'student_id' => $student->getKey(),
        'guardian_id' => null,
        'name' => 'Mme Aya Diop',
        'phone' => '+221 77 111 22 33',
        'email' => 'aya.embedded@example.test',
        'relationship' => 'parent',
        'is_primary' => true,
    ]);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Guardian,
        schoolId: $schoolId,
        email: 'aya.embedded@example.test',
        embeddedGuardianReference: true,
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::PossibleMatch)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::EmbeddedReferenceOnly)
        ->and($result->evidence)->toContain(IdentityEvidenceType::EmbeddedGuardianReference)
        ->and($result->candidateId)->toBeNull()
        ->and($result->automaticReusePermitted)->toBeFalse()
        ->and(Guardian::forSchool($schoolId)->where('email', 'aya.embedded@example.test')->exists())->toBeFalse();

    $embedded->refresh();

    expect($embedded->guardian_id)->toBeNull()
        ->and($embedded->name)->toBe('Mme Aya Diop')
        ->and($embedded->email)->toBe('aya.embedded@example.test')
        ->and($embedded->is_primary)->toBeTrue();
});

test('matching preserves guardian relationship flags and history', function (): void {
    $schoolId = makeTenant('Relationship Preservation School');
    task111InitializeTenant($schoolId);

    $student = Student::factory()->create(['school_id' => $schoolId]);
    $guardian = Guardian::factory()->create(['school_id' => $schoolId]);
    $relationship = task111DurableRelationship($schoolId, $student, $guardian, true);

    StudentGuardianRelationshipHistory::create([
        'school_id' => $schoolId,
        'student_guardian_id' => $relationship->getKey(),
        'student_id' => $student->getKey(),
        'guardian_id' => $guardian->getKey(),
        'event' => 'linked',
        'description' => 'Lien initial enregistré.',
        'occurred_at' => now(),
    ]);

    $before = $relationship->only([
        'guardian_id',
        'is_primary',
        'is_financial_responsible',
        'is_notification_recipient',
        'is_emergency_contact',
        'relationship_end_date',
    ]);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Guardian,
        schoolId: $schoolId,
        guardianId: $guardian->getKey(),
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::ExactMatch)
        ->and($result->automaticReusePermitted)->toBeTrue();

    $relationship->refresh();

    expect($relationship->only(array_keys($before)))->toBe($before)
        ->and(StudentGuardianRelationshipHistory::forSchool($schoolId)->count())->toBe(1)
        ->and(StudentGuardianRelationshipHistory::forSchool($schoolId)->first()->event)->toBe('linked');
});

test('suspended accounts cannot qualify for automatic reuse', function (): void {
    $schoolId = makeTenant('Suspended Account School');
    task111InitializeTenant($schoolId);

    $parent = task111ParentUser($schoolId, [
        'email' => 'suspended.parent@example.test',
        'status' => UserStatus::Suspended,
    ]);
    $student = Student::factory()->create(['school_id' => $schoolId]);
    $guardian = Guardian::factory()->create([
        'school_id' => $schoolId,
        'user_id' => $parent->getKey(),
        'email' => $parent->email,
    ]);
    task111DurableRelationship($schoolId, $student, $guardian);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::User,
        schoolId: $schoolId,
        email: 'suspended.parent@example.test',
        guardianId: $guardian->getKey(),
        studentId: $student->getKey(),
        sameSchoolCorroboration: true,
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::IneligibleAccount)
        ->and($result->evidence)->toContain(IdentityEvidenceType::IneligibleAccount)
        ->and($result->automaticReusePermitted)->toBeFalse();
});

test('matching results do not expose sensitive personal information', function (): void {
    $schoolId = makeTenant('Privacy School');
    task111InitializeTenant($schoolId);

    $email = 'private.parent@example.test';
    $phone = '+221 77 999 88 77';
    $token = bin2hex(random_bytes(32));

    task111ParentUser($schoolId, ['email' => $email, 'phone' => $phone]);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::User,
        schoolId: $schoolId,
        email: $email,
        phone: $phone,
    ));

    $serialized = json_encode($result, JSON_THROW_ON_ERROR);

    expect(property_exists($result, 'email'))->toBeFalse()
        ->and(property_exists($result, 'phone'))->toBeFalse()
        ->and(property_exists($result, 'token'))->toBeFalse()
        ->and(property_exists($result, 'password'))->toBeFalse()
        ->and($serialized)->not->toContain($email)
        ->and($serialized)->not->toContain($phone)
        ->and($serialized)->not->toContain($token)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::ExactEmailWithoutCorroboration);
});

test('email comparison follows the account provisioning normalization rule', function (): void {
    $schoolId = makeTenant('Normalization School');
    task111InitializeTenant($schoolId);

    task111ParentUser($schoolId, ['email' => 'normalized.parent@example.test']);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::User,
        schoolId: $schoolId,
        email: '  NORMALIZED.PARENT@EXAMPLE.TEST  ',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::PossibleMatch)
        ->and($result->candidateFound)->toBeTrue()
        ->and($result->evidence)->toContain(IdentityEvidenceType::ExactNormalizedEmail)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::ExactEmailWithoutCorroboration)
        ->and($result->automaticReusePermitted)->toBeFalse();
});

test('inactive and pending accounts cannot qualify for automatic reuse', function (string $status): void {
    $schoolId = makeTenant('Ineligible Status School');
    task111InitializeTenant($schoolId);

    $parent = task111ParentUser($schoolId, [
        'email' => 'ineligible.status@example.test',
        'status' => UserStatus::from($status),
    ]);
    $student = Student::factory()->create(['school_id' => $schoolId]);
    $guardian = Guardian::factory()->create([
        'school_id' => $schoolId,
        'user_id' => $parent->getKey(),
        'email' => $parent->email,
    ]);
    task111DurableRelationship($schoolId, $student, $guardian);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::User,
        schoolId: $schoolId,
        email: $parent->email,
        guardianId: $guardian->getKey(),
        studentId: $student->getKey(),
        sameSchoolCorroboration: true,
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::IneligibleAccount)
        ->and($result->automaticReusePermitted)->toBeFalse();
})->with([UserStatus::Inactive->value, UserStatus::Pending->value]);

test('a same-school user without the parent role cannot qualify for automatic reuse', function (): void {
    $schoolId = makeTenant('Role Compatibility School');
    task111InitializeTenant($schoolId);

    Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);

    /** @var User $user */
    $user = User::factory()->create([
        'school_id' => $schoolId,
        'email' => 'teacher.account@example.test',
        'status' => UserStatus::Active,
    ]);
    $user->assignRole('teacher');

    $student = Student::factory()->create(['school_id' => $schoolId]);
    $guardian = Guardian::factory()->create([
        'school_id' => $schoolId,
        'user_id' => $user->getKey(),
        'email' => $user->email,
    ]);
    task111DurableRelationship($schoolId, $student, $guardian);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::User,
        schoolId: $schoolId,
        email: $user->email,
        guardianId: $guardian->getKey(),
        studentId: $student->getKey(),
        sameSchoolCorroboration: true,
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::RoleNotCompatible)
        ->and($result->automaticReusePermitted)->toBeFalse();
});

test('soft-deleted student and guardian records cannot be automatically reused', function (): void {
    $schoolId = makeTenant('Soft Deleted Records School');
    task111InitializeTenant($schoolId);

    $student = Student::factory()->create([
        'school_id' => $schoolId,
        'student_number' => 'STU-DELETED',
    ]);
    $guardian = Guardian::factory()->create([
        'school_id' => $schoolId,
        'guardian_number' => 'GAR-DELETED',
    ]);
    $student->delete();
    $guardian->delete();

    $studentResult = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Student,
        schoolId: $schoolId,
        studentNumber: 'STU-DELETED',
    ));
    $guardianResult = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Guardian,
        schoolId: $schoolId,
        guardianNumber: 'GAR-DELETED',
    ));

    expect($studentResult->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($studentResult->reasonCodes)->toContain(IdentityMatchReasonCode::SoftDeletedCandidate)
        ->and($studentResult->automaticReusePermitted)->toBeFalse()
        ->and($guardianResult->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($guardianResult->reasonCodes)->toContain(IdentityMatchReasonCode::SoftDeletedCandidate)
        ->and($guardianResult->automaticReusePermitted)->toBeFalse();
});

test('student and guardian candidates in another school cannot be reused', function (): void {
    $schoolA = makeTenant('Local Records School');
    $schoolB = makeTenant('Foreign Records School');
    task111InitializeTenant($schoolA);

    Student::factory()->create([
        'school_id' => $schoolB,
        'student_number' => 'STU-FOREIGN',
    ]);
    Guardian::factory()->create([
        'school_id' => $schoolB,
        'guardian_number' => 'GAR-FOREIGN',
    ]);

    $studentResult = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Student,
        schoolId: $schoolA,
        studentNumber: 'STU-FOREIGN',
    ));
    $guardianResult = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Guardian,
        schoolId: $schoolA,
        guardianNumber: 'GAR-FOREIGN',
    ));

    expect($studentResult->outcome)->toBe(IdentityMatchOutcome::PossibleMatch)
        ->and($studentResult->reasonCodes)->toContain(IdentityMatchReasonCode::NoCandidate)
        ->and($studentResult->candidateId)->toBeNull()
        ->and($studentResult->automaticReusePermitted)->toBeFalse()
        ->and($guardianResult->outcome)->toBe(IdentityMatchOutcome::PossibleMatch)
        ->and($guardianResult->reasonCodes)->toContain(IdentityMatchReasonCode::NoCandidate)
        ->and($guardianResult->candidateId)->toBeNull()
        ->and($guardianResult->automaticReusePermitted)->toBeFalse();
});

test('embedded guardian references are detected without an explicit caller flag', function (): void {
    $schoolId = makeTenant('Implicit Embedded School');
    task111InitializeTenant($schoolId);

    $student = Student::factory()->create(['school_id' => $schoolId]);
    StudentGuardian::create([
        'school_id' => $schoolId,
        'student_id' => $student->getKey(),
        'guardian_id' => null,
        'name' => 'M. Ousmane Sarr',
        'phone' => '+221 77 222 33 44',
        'email' => 'implicit.embedded@example.test',
        'relationship' => 'parent',
        'is_primary' => false,
    ]);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Guardian,
        schoolId: $schoolId,
        email: 'implicit.embedded@example.test',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::PossibleMatch)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::EmbeddedReferenceOnly)
        ->and($result->evidence)->toContain(IdentityEvidenceType::EmbeddedGuardianReference)
        ->and($result->candidateId)->toBeNull()
        ->and($result->automaticReusePermitted)->toBeFalse()
        ->and(Guardian::forSchool($schoolId)->where('email', 'implicit.embedded@example.test')->exists())->toBeFalse()
        ->and(StudentGuardian::forSchool($schoolId)->where('guardian_id', null)->count())->toBe(1);
});

test('name plus date of birth remains a possible match only', function (): void {
    $schoolId = makeTenant('Date Of Birth School');
    task111InitializeTenant($schoolId);

    Student::factory()->create([
        'school_id' => $schoolId,
        'first_name' => 'Amina',
        'last_name' => 'Diallo',
        'date_of_birth' => '2012-05-14',
    ]);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Student,
        schoolId: $schoolId,
        firstName: 'Amina',
        lastName: 'Diallo',
        dateOfBirth: '2012-05-14',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::PossibleMatch)
        ->and($result->evidence)->toContain(
            IdentityEvidenceType::NameSimilarity,
            IdentityEvidenceType::DateOfBirthSupport,
        )
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::NameAndDateOfBirthOnly)
        ->and($result->automaticReusePermitted)->toBeFalse();
});

test('conflicting date of birth evidence produces a conflict', function (): void {
    $schoolId = makeTenant('Date Conflict School');
    task111InitializeTenant($schoolId);

    Student::factory()->create([
        'school_id' => $schoolId,
        'student_number' => 'STU-DOB',
        'date_of_birth' => '2012-05-14',
    ]);

    $result = task111Matcher()->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Student,
        schoolId: $schoolId,
        studentNumber: 'STU-DOB',
        dateOfBirth: '2013-01-01',
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::AmbiguousOrConflict)
        ->and($result->reasonCodes)->toContain(IdentityMatchReasonCode::ConflictingEvidence)
        ->and($result->evidence)->toContain(IdentityEvidenceType::ConflictingIdentity)
        ->and($result->automaticReusePermitted)->toBeFalse();
});
