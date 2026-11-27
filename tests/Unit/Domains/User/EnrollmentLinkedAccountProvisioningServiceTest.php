<?php

declare(strict_types=1);

use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\IdentityMatching\Application\DTOs\IdentityMatchRequest;
use App\Domains\IdentityMatching\Application\Services\SchoolScopedIdentityMatcher;
use App\Domains\IdentityMatching\Domain\Enums\IdentityMatchOutcome;
use App\Domains\IdentityMatching\Domain\Enums\MatchableEntityType;
use App\Domains\School\Models\School;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\Student\Infrastructure\Models\StudentGuardian;
use App\Domains\User\DTOs\AccountProvisioningCommand;
use App\Domains\User\DTOs\AccountProvisioningContext;
use App\Domains\User\DTOs\EnrollmentProvisioningRequest;
use App\Domains\User\DTOs\ParentPortalAccessRequest;
use App\Domains\User\DTOs\StudentEligibilityEvidence;
use App\Domains\User\Enums\EnrollmentProvisioningRejectionCode;
use App\Domains\User\Enums\UserStatus;
use App\Domains\User\Exceptions\AccountInvitationActivationException;
use App\Domains\User\Exceptions\AccountProvisioningException;
use App\Domains\User\Exceptions\EnrollmentProvisioningException;
use App\Domains\User\Services\AccountInvitationActivationService;
use App\Domains\User\Services\AccountProvisioningService;
use App\Domains\User\Services\EnrollmentLinkedAccountProvisioningService;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\PanelAccessRegistry;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

afterEach(function (): void {
    if (tenancy()->initialized) {
        tenancy()->end();
    }
});

function task112Tenant(string $name = 'Task 11.2 School'): string
{
    return makeTenant($name);
}

function task112Initialize(string $schoolId): void
{
    tenancy()->initialize($schoolId);
}

function task112Role(string $name): Role
{
    return Role::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']);
}

function task112Actor(
    string $schoolId,
    string $roleName = 'school_admin',
    UserStatus $status = UserStatus::Active,
): User {
    task112Role($roleName);

    /** @var User $actor */
    $actor = User::factory()->create([
        'school_id' => $schoolId,
        'status' => $status,
        'email' => 'task112-actor-'.Str::lower(Str::random(12)).'@example.test',
    ]);
    $actor->assignRole($roleName);

    return $actor;
}

/**
 * @return array<string, mixed>
 */
function task112StudentAttributes(string $schoolId, string $suffix): array
{
    return [
        'school_id' => $schoolId,
        'student_number' => 'STU-112-'.$suffix,
        'admission_number' => 'ADM-112-'.$suffix,
        'first_name' => 'Amina',
        'last_name' => 'Diallo',
        'email' => 'student-'.$suffix.'@example.test',
        'phone_number' => '+221 77 112 00 '.$suffix,
    ];
}

function task112Student(string $schoolId, string $suffix = '01'): Student
{
    return Student::factory()->create(task112StudentAttributes($schoolId, $suffix));
}

function task112Enrollment(
    string $schoolId,
    Student $student,
    EnrollmentStatus $status = EnrollmentStatus::Active,
): Enrollment {
    return Enrollment::factory()->create([
        'school_id' => $schoolId,
        'student_id' => $student->getKey(),
        'status' => $status,
    ]);
}

function task112Guardian(string $schoolId, string $suffix = '01'): Guardian
{
    return Guardian::factory()->create([
        'school_id' => $schoolId,
        'guardian_number' => 'GUA-112-'.$suffix,
        'first_name' => 'Fatou'.$suffix,
        'last_name' => 'Sow',
        'email' => 'guardian-'.$suffix.'@example.test',
        'phone_number' => '+221 77 112 10 '.$suffix,
    ]);
}

function task112Relationship(string $schoolId, Student $student, Guardian $guardian): StudentGuardianRelationship
{
    return StudentGuardianRelationship::create([
        'school_id' => $schoolId,
        'student_id' => $student->getKey(),
        'guardian_id' => $guardian->getKey(),
        'name' => $guardian->getAttribute('first_name').' '.$guardian->getAttribute('last_name'),
        'phone' => $guardian->getAttribute('phone_number'),
        'email' => $guardian->getAttribute('email'),
        'relationship' => 'mother',
        'is_primary' => true,
        'is_emergency_contact' => true,
        'is_authorized_pickup' => true,
        'is_financial_responsible' => true,
        'is_notification_recipient' => true,
        'is_medical_contact' => false,
    ]);
}

function task112Evidence(bool $eligible = true, bool $contact = true): StudentEligibilityEvidence
{
    return new StudentEligibilityEvidence($eligible, $contact, 'DOSSIER-INTERNE-112');
}

function task112PortalRequest(int|string $guardianId, bool $requested = true, bool $specific = true): ParentPortalAccessRequest
{
    return new ParentPortalAccessRequest($guardianId, $requested, $specific);
}

function task112StudentRequest(
    string $schoolId,
    Enrollment $enrollment,
    User $actor,
    Student $student,
    bool $explicit = true,
    ?StudentEligibilityEvidence $evidence = null,
    ?string $email = null,
): EnrollmentProvisioningRequest {
    task112Role('student');

    return EnrollmentProvisioningRequest::forStudent(
        schoolId: $schoolId,
        enrollmentId: (string) $enrollment->getKey(),
        actor: $actor,
        explicitSchoolRequest: $explicit,
        studentEvidence: $evidence ?? task112Evidence(),
        email: $email ?? (string) $student->getAttribute('email'),
        firstName: (string) $student->getAttribute('first_name'),
        lastName: (string) $student->getAttribute('last_name'),
        phone: $student->getAttribute('phone_number'),
    );
}

function task112ParentRequest(
    string $schoolId,
    Enrollment $enrollment,
    User $actor,
    Guardian $guardian,
    bool $explicit = true,
    ?ParentPortalAccessRequest $portalRequest = null,
    ?string $email = null,
): EnrollmentProvisioningRequest {
    task112Role('parent');

    return EnrollmentProvisioningRequest::forParent(
        schoolId: $schoolId,
        enrollmentId: (string) $enrollment->getKey(),
        actor: $actor,
        explicitSchoolRequest: $explicit,
        parentRequest: $portalRequest ?? task112PortalRequest($guardian->getKey()),
        email: $email ?? (string) $guardian->getAttribute('email'),
        firstName: (string) $guardian->getAttribute('first_name'),
        lastName: (string) $guardian->getAttribute('last_name'),
        phone: $guardian->getAttribute('phone_number'),
    );
}

function task112Service(): EnrollmentLinkedAccountProvisioningService
{
    return new EnrollmentLinkedAccountProvisioningService();
}

function task112ExpectRejection(
    callable $callback,
    EnrollmentProvisioningRejectionCode $expectedCode,
): EnrollmentProvisioningException {
    try {
        $callback();
    } catch (EnrollmentProvisioningException $exception) {
        expect($exception->reasonCode)->toBe($expectedCode);

        return $exception;
    }

    throw new RuntimeException('Expected an EnrollmentProvisioningException.');
}

test('ordinary student and enrollment registration creates no authentication account', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);

    $student = task112Student($schoolId);
    task112Enrollment($schoolId, $student);

    expect(User::query()->where('school_id', $schoolId)->count())->toBe(0)
        ->and(UserInvitation::query()->where('school_id', $schoolId)->count())->toBe(0);
});

test('an explicit eligible student request provisions a pending account without a usable password', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);

    $result = task112Service()->provision(
        task112StudentRequest($schoolId, $enrollment, $actor, $student),
    );

    expect($result->userCreated)->toBeTrue()
        ->and($result->user->status)->toBe(UserStatus::Pending)
        ->and($result->user->hasRole('student'))->toBeTrue()
        ->and((string) $result->user->school_id)->toBe($schoolId)
        ->and(Hash::check('password', (string) $result->user->password))->toBeFalse()
        ->and($result->invitation->purpose)->toBe(UserInvitation::PURPOSE_STUDENT_INITIAL)
        ->and($result->deliveryBlocked())->toBeTrue()
        ->and((string) $student->fresh()->user_id)->toBe('')
        ->and(json_encode($result, JSON_THROW_ON_ERROR))->not->toContain('plainToken')
        ->and(json_encode($result, JSON_THROW_ON_ERROR))->not->toContain('token_hash');
});

test('a student request without an explicit school request is rejected', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112StudentRequest($schoolId, $enrollment, $actor, $student, explicit: false),
        ),
        EnrollmentProvisioningRejectionCode::ExplicitRequestMissing,
    );

    expect(User::query()->where('school_id', $schoolId)->count())->toBe(1);
});

test('a student request without complete eligibility and contact evidence is rejected', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112StudentRequest(
                $schoolId,
                $enrollment,
                $actor,
                $student,
                evidence: task112Evidence(eligible: false),
            ),
        ),
        EnrollmentProvisioningRejectionCode::EligibilityMissing,
    );

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112StudentRequest(
                $schoolId,
                $enrollment,
                $actor,
                $student,
                evidence: task112Evidence(contact: false),
            ),
        ),
        EnrollmentProvisioningRejectionCode::ContactEvidenceMissing,
    );

    expect(User::query()->where('school_id', $schoolId)->count())->toBe(1);
});

test('an eligible same-school student user is reused through task 11.1 exact matching', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);

    task112Role('student');
    /** @var User $studentUser */
    $studentUser = User::factory()->create([
        'school_id' => $schoolId,
        'status' => UserStatus::Active,
        'email' => $student->email,
        'phone' => $student->phone_number,
        'first_name' => $student->first_name,
        'last_name' => $student->last_name,
    ]);
    $studentUser->assignRole('student');
    $student->forceFill(['user_id' => $studentUser->getKey()])->save();

    $result = task112Service()->provision(
        task112StudentRequest($schoolId, $enrollment, $actor, $student),
    );

    expect($result->userCreated)->toBeFalse()
        ->and($result->user->getKey())->toBe($studentUser->getKey())
        ->and(User::query()
            ->where('school_id', $schoolId)
            ->whereHas('roles', fn ($query) => $query->where('name', 'student'))
            ->count())->toBe(1);
});

test('a cross-school student user email blocks provisioning as a conflict', function (): void {
    $schoolId = task112Tenant('Local Student School');
    $foreignSchoolId = task112Tenant('Foreign Student School');
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);

    task112Role('student');
    User::factory()->create([
        'school_id' => $foreignSchoolId,
        'status' => UserStatus::Active,
        'email' => 'cross.student.identity@example.test',
    ]);

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112StudentRequest(
                $schoolId,
                $enrollment,
                $actor,
                $student,
                email: 'cross.student.identity@example.test',
            ),
        ),
        EnrollmentProvisioningRejectionCode::IdentityConflict,
    );

    expect(User::query()->where('school_id', $schoolId)->where('email', 'cross.student.identity@example.test')->count())->toBe(0);
});

test('ambiguous student identity does not create or reuse an account', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);

    Student::factory()->create([
        'school_id' => $schoolId,
        'phone_number' => $student->phone_number,
        'first_name' => $student->first_name,
        'last_name' => $student->last_name,
    ]);

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112StudentRequest($schoolId, $enrollment, $actor, $student),
        ),
        EnrollmentProvisioningRejectionCode::IdentityConflict,
    );

    expect(User::query()->where('school_id', $schoolId)->whereHas('roles', fn ($query) => $query->where('name', 'student'))->count())->toBe(0);
});

test('a suspended, inactive or soft-deleted student user cannot be reused', function (UserStatus $status): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);

    task112Role('student');
    /** @var User $candidate */
    $candidate = User::factory()->create([
        'school_id' => $schoolId,
        'status' => $status,
        'email' => $student->email,
        'phone' => $student->phone_number,
        'first_name' => $student->first_name,
        'last_name' => $student->last_name,
    ]);
    $candidate->assignRole('student');
    $student->forceFill(['user_id' => $candidate->getKey()])->save();

    if ($status === UserStatus::Inactive) {
        $candidate->delete();
    }

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112StudentRequest($schoolId, $enrollment, $actor, $student),
        ),
        EnrollmentProvisioningRejectionCode::IdentityConflict,
    );

    expect(User::withTrashed()->where('school_id', $schoolId)->count())->toBe(2);
})->with([
    'suspended' => [UserStatus::Suspended],
    'inactive' => [UserStatus::Inactive],
]);

test('an unconfirmed enrollment cannot trigger a guardian invitation', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student, EnrollmentStatus::Suspended);
    $guardian = task112Guardian($schoolId);
    task112Relationship($schoolId, $student, $guardian);

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112ParentRequest($schoolId, $enrollment, $actor, $guardian),
        ),
        EnrollmentProvisioningRejectionCode::EnrollmentNotConfirmed,
    );

    expect(UserInvitation::query()->where('school_id', $schoolId)->count())->toBe(0);
});

test('an invitation requires an explicit portal-access request for the selected guardian', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);
    $guardian = task112Guardian($schoolId);
    task112Relationship($schoolId, $student, $guardian);

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112ParentRequest(
                $schoolId,
                $enrollment,
                $actor,
                $guardian,
                explicit: false,
            ),
        ),
        EnrollmentProvisioningRejectionCode::ExplicitRequestMissing,
    );

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112ParentRequest(
                $schoolId,
                $enrollment,
                $actor,
                $guardian,
                portalRequest: task112PortalRequest($guardian->getKey(), requested: false),
            ),
        ),
        EnrollmentProvisioningRejectionCode::ExplicitRequestMissing,
    );

    expect(UserInvitation::query()->where('school_id', $schoolId)->count())->toBe(0);
});

test('a guardian invitation targets only the explicitly selected guardian and leaves records unlinked', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);
    $selected = task112Guardian($schoolId, '01');
    $other = task112Guardian($schoolId, '02');
    task112Relationship($schoolId, $student, $selected);
    task112Relationship($schoolId, $student, $other);
    $relationshipCount = StudentGuardianRelationship::query()->where('school_id', $schoolId)->count();

    $result = task112Service()->provision(
        task112ParentRequest($schoolId, $enrollment, $actor, $selected),
    );

    expect($result->guardianId)->toBe((string) $selected->getKey())
        ->and((string) $selected->fresh()->user_id)->toBe('')
        ->and((string) $other->fresh()->user_id)->toBe('')
        ->and(StudentGuardianRelationship::query()->where('school_id', $schoolId)->count())->toBe($relationshipCount)
        ->and(UserInvitation::query()->where('school_id', $schoolId)->count())->toBe(1);
});

test('embedded guardian references are never promoted to guardian, user or durable links', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);

    StudentGuardian::create([
        'school_id' => $schoolId,
        'student_id' => $student->getKey(),
        'guardian_id' => null,
        'name' => 'Embedded Parent',
        'phone' => $student->phone_number,
        'email' => $student->email,
        'relationship' => 'mother',
        'is_primary' => true,
    ]);

    $guardianCount = Guardian::query()->where('school_id', $schoolId)->count();
    $relationshipCount = StudentGuardianRelationship::query()->where('school_id', $schoolId)->count();

    task112ExpectRejection(
        fn () => task112Service()->provision(
            EnrollmentProvisioningRequest::forParent(
                schoolId: $schoolId,
                enrollmentId: (string) $enrollment->getKey(),
                actor: $actor,
                explicitSchoolRequest: true,
                parentRequest: task112PortalRequest('missing-guardian'),
                email: (string) $student->getAttribute('email'),
                firstName: 'Embedded',
                lastName: 'Parent',
                phone: $student->getAttribute('phone_number'),
            ),
        ),
        EnrollmentProvisioningRejectionCode::GuardianNotSelected,
    );

    expect(Guardian::query()->where('school_id', $schoolId)->count())->toBe($guardianCount)
        ->and(StudentGuardianRelationship::query()->where('school_id', $schoolId)->count())->toBe($relationshipCount)
        ->and(StudentGuardian::query()->where('school_id', $schoolId)->whereNotNull('guardian_id')->count())->toBe(0);
});

test('a cross-school parent identity cannot be reused', function (): void {
    $schoolId = task112Tenant('Local Parent School');
    $foreignSchoolId = task112Tenant('Foreign Parent School');
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);
    $guardian = task112Guardian($schoolId);
    task112Relationship($schoolId, $student, $guardian);

    task112Role('parent');
    User::factory()->create([
        'school_id' => $foreignSchoolId,
        'status' => UserStatus::Active,
        'email' => 'cross.parent.identity@example.test',
    ]);

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112ParentRequest(
                $schoolId,
                $enrollment,
                $actor,
                $guardian,
                email: 'cross.parent.identity@example.test',
            ),
        ),
        EnrollmentProvisioningRejectionCode::IdentityConflict,
    );

    expect(User::query()->where('school_id', $schoolId)->where('email', 'cross.parent.identity@example.test')->count())->toBe(0);
});

test('ambiguous parent matching blocks provisioning', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);
    $guardian = task112Guardian($schoolId);
    task112Relationship($schoolId, $student, $guardian);

    task112Role('parent');
    foreach (['one', 'two'] as $suffix) {
        $candidate = User::factory()->create([
            'school_id' => $schoolId,
            'status' => UserStatus::Active,
            'email' => 'ambiguous-parent-'.$suffix.'@example.test',
            'phone' => $guardian->phone_number,
            'first_name' => $guardian->first_name,
            'last_name' => $guardian->last_name,
        ]);
        $candidate->assignRole('parent');
    }
    $guardian->forceFill(['user_id' => $candidate->getKey()])->save();

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112ParentRequest($schoolId, $enrollment, $actor, $guardian),
        ),
        EnrollmentProvisioningRejectionCode::IdentityConflict,
    );

    expect(User::query()->where('school_id', $schoolId)->whereHas('roles', fn ($query) => $query->where('name', 'parent'))->count())->toBe(2);
});

test('an eligible same-school parent user is reused through task 11.1 exact matching', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);
    $guardian = task112Guardian($schoolId);
    task112Relationship($schoolId, $student, $guardian);

    task112Role('parent');
    /** @var User $parent */
    $parent = User::factory()->create([
        'school_id' => $schoolId,
        'status' => UserStatus::Active,
        'email' => $guardian->email,
        'phone' => $guardian->phone_number,
        'first_name' => $guardian->first_name,
        'last_name' => $guardian->last_name,
    ]);
    $parent->assignRole('parent');
    $guardian->forceFill(['user_id' => $parent->getKey()])->save();

    $result = task112Service()->provision(
        task112ParentRequest($schoolId, $enrollment, $actor, $guardian),
    );

    expect($result->userCreated)->toBeFalse()
        ->and($result->user->getKey())->toBe($parent->getKey())
        ->and($result->invitation->purpose)->toBe(UserInvitation::PURPOSE_GUARDIAN_INITIAL)
        ->and($result->deliveryBlocked())->toBeTrue();
});

test('parent activation remains blocked until child-level authorization exists', function (): void {
    $school = School::query()->findOrFail(task112Tenant());
    $schoolId = (string) $school->getKey();
    task112Initialize($schoolId);

    task112Role('parent');
    /** @var User $parent */
    $parent = User::factory()->create([
        'school_id' => $schoolId,
        'status' => UserStatus::Pending,
    ]);
    $parent->assignRole('parent');
    $issue = UserInvitation::issue($parent, UserInvitation::PURPOSE_GUARDIAN_INITIAL);

    try {
        (new AccountInvitationActivationService())->activate(
            $issue->plainToken,
            'password-112',
            $school,
        );
        throw new RuntimeException('Expected family activation to be blocked.');
    } catch (AccountInvitationActivationException $exception) {
        expect($exception->getMessage())->toContain('autorisation par élève');
    }

    expect($parent->fresh()->status)->toBe(UserStatus::Pending)
        ->and($issue->invitation->fresh()->consumed_at)->toBeNull()
        ->and((new PanelAccessRegistry())->canUserAccessPanel($parent->fresh(), 'parent'))->toBeFalse();
});

test('the public activation endpoint cannot activate a pending parent account before task 11.3', function (): void {
    $school = School::query()->findOrFail(task112Tenant());
    $schoolId = (string) $school->getKey();
    task112Initialize($schoolId);

    task112Role('parent');
    /** @var User $parent */
    $parent = User::factory()->create([
        'school_id' => $schoolId,
        'status' => UserStatus::Pending,
    ]);
    $parent->assignRole('parent');
    $issue = UserInvitation::issue($parent, UserInvitation::PURPOSE_GUARDIAN_INITIAL);

    $response = $this->post(route('school.account.activation.store', ['token' => $issue->plainToken]), [
        'password' => 'password-112',
        'password_confirmation' => 'password-112',
    ]);

    $response->assertStatus(404);
    $this->assertGuest();
    expect($parent->fresh()->status)->toBe(UserStatus::Pending);
});

test('an existing usable same-purpose invitation is reused without rotation', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    [$service, $request] = task112PreparedParentScenario($schoolId);

    $first = $service->provision($request);
    $second = $service->provision($request);

    expect($second->invitation->lifecycle->value)->toBe('reused')
        ->and($second->invitation->invitationId)->toBe($first->invitation->invitationId)
        ->and(UserInvitation::query()->where('user_id', $first->user->getKey())->count())->toBe(1);
});

test('duplicate active invitations are prevented for the same identity and purpose', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    [$service, $request] = task112PreparedParentScenario($schoolId);

    $service->provision($request);
    $service->provision($request);
    $service->provision($request);

    $usable = UserInvitation::query()
        ->where('school_id', $schoolId)
        ->where('purpose', UserInvitation::PURPOSE_GUARDIAN_INITIAL)
        ->whereNull('consumed_at')
        ->whereNull('revoked_at')
        ->where('expires_at', '>', now())
        ->count();

    expect($usable)->toBeLessThanOrEqual(1);
});

test('expired invitation reissue preserves the user guardian and relationship history', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    [$service, $request, $context] = task112PreparedParentScenario($schoolId);

    $expired = UserInvitation::issue(
        $context['user'],
        UserInvitation::PURPOSE_GUARDIAN_INITIAL,
        $context['actor'],
        now()->subHour(),
    )->invitation;

    $result = $service->provision($request);

    expect($result->invitation->lifecycle->value)->toBe('reissued')
        ->and($expired->fresh()->revoked_at)->not->toBeNull()
        ->and($result->user->exists)->toBeTrue()
        ->and($context['guardian']->fresh()->exists)->toBeTrue()
        ->and(StudentGuardianRelationship::query()->whereKey($context['relationship']->getKey())->exists())->toBeTrue();
});

test('reissue uses the existing forty-eight hour invitation lifetime', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    [$service, $request, $context] = task112PreparedParentScenario($schoolId);

    $expired = UserInvitation::issue(
        $context['user'],
        UserInvitation::PURPOSE_GUARDIAN_INITIAL,
        $context['actor'],
        now()->subHour(),
    )->invitation;

    $before = now();
    $result = $service->provision($request);
    $newInvitation = UserInvitation::query()->whereKey($result->invitation->invitationId)->firstOrFail();

    $lifetimeSeconds = abs($newInvitation->expires_at->diffInSeconds($before));
    $expectedSeconds = UserInvitation::EXPIRATION_HOURS * 3600;

    expect($expired->fresh()->revoked_at)->not->toBeNull()
        ->and($lifetimeSeconds)->toBeGreaterThanOrEqual($expectedSeconds - 5)
        ->toBeLessThanOrEqual($expectedSeconds + 5);
});

test('a different-purpose outstanding invitation is revoked before reissue', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    [$service, $request, $context] = task112PreparedParentScenario($schoolId);

    $otherPurpose = UserInvitation::issue(
        $context['user'],
        UserInvitation::PURPOSE_STAFF_INITIAL,
        $context['actor'],
    )->invitation;

    $result = $service->provision($request);

    expect($otherPurpose->fresh()->revoked_at)->not->toBeNull()
        ->and($result->invitation->lifecycle->value)->toBe('reissued')
        ->and(UserInvitation::query()
            ->where('user_id', $context['user']->getKey())
            ->whereNull('consumed_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->count())->toBe(1);
});

test('sequential concurrent-style requests cannot leave conflicting usable invitations', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    [$service, $request, $context] = task112PreparedParentScenario($schoolId);

    $first = $service->provision($request);
    $second = (new EnrollmentLinkedAccountProvisioningService())->provision($request);

    expect($first->invitation->invitationId)->toBe($second->invitation->invitationId)
        ->and(UserInvitation::query()
            ->where('user_id', $context['user']->getKey())
            ->whereNull('consumed_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->count())->toBe(1);
});

test('only invitation token hashes are persisted and no secret appears in the result or audit', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    [$service, $request] = task112PreparedParentScenario($schoolId);

    $result = $service->provision($request);
    $invitation = UserInvitation::query()->whereKey($result->invitation->invitationId)->firstOrFail();
    $hash = (string) $invitation->getAttribute('token_hash');

    expect($hash)->toMatch('/^[a-f0-9]{64}$/')
        ->and(json_encode($result, JSON_THROW_ON_ERROR))->not->toContain($hash)
        ->and(json_encode($result, JSON_THROW_ON_ERROR))->not->toContain('token_hash')
        ->and(json_encode(AuditLog::query()->where('school_id', $schoolId)->get()->all(), JSON_THROW_ON_ERROR))
        ->not->toContain($hash);
});

test('school admin authorization is limited to the approved enrollment-linked workflow', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    task112Role('parent');

    expect(fn () => (new AccountProvisioningService())->provision(
        new AccountProvisioningCommand(
            schoolId: $schoolId,
            firstName: 'Forbidden',
            lastName: 'Parent',
            email: 'forbidden-parent@example.test',
            roleName: 'parent',
        ),
        AccountProvisioningContext::forSchoolAdmin($actor),
    ))->toThrow(AccountProvisioningException::class);
});

test('a crafted school identifier cannot cross tenant boundaries', function (): void {
    $schoolId = task112Tenant('Current School');
    $foreignSchoolId = task112Tenant('Crafted School');
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112StudentRequest($foreignSchoolId, $enrollment, $actor, $student),
        ),
        EnrollmentProvisioningRejectionCode::TenantContextMismatch,
    );

    expect(User::query()->where('school_id', $foreignSchoolId)->count())->toBe(0)
        ->and(UserInvitation::query()->where('school_id', $foreignSchoolId)->count())->toBe(0);
});

test('a missing tenant context fails closed', function (): void {
    $schoolId = task112Tenant();
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112StudentRequest($schoolId, $enrollment, $actor, $student),
        ),
        EnrollmentProvisioningRejectionCode::TenantContextMissing,
    );

    expect(UserInvitation::query()->where('school_id', $schoolId)->count())->toBe(0);
});

test('an inactive actor cannot provision an enrollment-linked account', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId, status: UserStatus::Inactive);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112StudentRequest($schoolId, $enrollment, $actor, $student),
        ),
        EnrollmentProvisioningRejectionCode::ActorInactive,
    );
});

test('a director cannot use the school-admin-only provisioning workflow', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $actor = task112Actor($schoolId, roleName: 'director');
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);

    task112ExpectRejection(
        fn () => task112Service()->provision(
            task112StudentRequest($schoolId, $enrollment, $actor, $student),
        ),
        EnrollmentProvisioningRejectionCode::ActorNotAuthorized,
    );
});

test('the exact-match foundation still supports only the three public outcomes', function (): void {
    $schoolId = task112Tenant();
    task112Initialize($schoolId);
    $student = task112Student($schoolId);

    $result = (new SchoolScopedIdentityMatcher())->match(new IdentityMatchRequest(
        entityType: MatchableEntityType::Student,
        schoolId: $schoolId,
        studentNumber: (string) $student->getAttribute('student_number'),
    ));

    expect($result->outcome)->toBe(IdentityMatchOutcome::ExactMatch)
        ->and(array_column(IdentityMatchOutcome::cases(), 'value'))->toBe([
            'EXACT_MATCH',
            'POSSIBLE_MATCH',
            'AMBIGUOUS_OR_CONFLICT',
        ]);
});

/**
 * @return array{0: EnrollmentLinkedAccountProvisioningService, 1: EnrollmentProvisioningRequest, 2: array<string, mixed>}
 */
function task112PreparedParentScenario(string $schoolId): array
{
    $actor = task112Actor($schoolId);
    $student = task112Student($schoolId);
    $enrollment = task112Enrollment($schoolId, $student);
    $guardian = task112Guardian($schoolId);
    $relationship = task112Relationship($schoolId, $student, $guardian);

    task112Role('parent');
    /** @var User $parent */
    $parent = User::factory()->create([
        'school_id' => $schoolId,
        'status' => UserStatus::Active,
        'email' => $guardian->email,
        'phone' => $guardian->phone_number,
        'first_name' => $guardian->first_name,
        'last_name' => $guardian->last_name,
    ]);
    $parent->assignRole('parent');
    $guardian->forceFill(['user_id' => $parent->getKey()])->save();

    return [
        task112Service(),
        task112ParentRequest($schoolId, $enrollment, $actor, $guardian),
        [
            'actor' => $actor,
            'student' => $student,
            'enrollment' => $enrollment,
            'guardian' => $guardian,
            'relationship' => $relationship,
            'user' => $parent,
        ],
    ];
}
