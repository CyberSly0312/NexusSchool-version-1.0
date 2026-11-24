<?php

declare(strict_types=1);

use App\Domains\School\Models\School;
use App\Domains\User\DTOs\AccountProvisioningCommand;
use App\Domains\User\DTOs\AccountProvisioningContext;
use App\Domains\User\DTOs\AccountProvisioningResult;
use App\Domains\User\Enums\UserStatus;
use App\Domains\User\Exceptions\AccountProvisioningException;
use App\Domains\User\Services\AccountProvisioningService;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

function createProvisioningSchool(string $status = 'active'): School
{
    $statusEnum = match ($status) {
        'active' => \App\Domains\School\Enums\SchoolStatus::Active,
        'inactive' => \App\Domains\School\Enums\SchoolStatus::Inactive,
        'pending' => \App\Domains\School\Enums\SchoolStatus::Pending,
        'suspended' => \App\Domains\School\Enums\SchoolStatus::Suspended,
        default => throw new InvalidArgumentException("Unknown status [{$status}]"),
    };

    $name = 'Provisioning School '.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6));

    return School::query()->create([
        'uuid' => (string) \Illuminate\Support\Str::ulid(),
        'name' => $name,
        'slug' => \Illuminate\Support\Str::slug($name),
        'database' => 'school_'.\Illuminate\Support\Str::slug($name),
        'domain' => \Illuminate\Support\Str::slug($name).'.nexus.school',
        'email' => \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(8)).'@example.test',
        'phone' => '+237600000000',
        'address' => '123 Test Street',
        'city' => 'Test City',
        'state' => 'Test State',
        'country' => 'Test Country',
        'postal_code' => '00000',
        'timezone' => 'UTC',
        'currency' => 'XAF',
        'locale' => 'en',
        'logo_url' => null,
        'registration_number' => 'REG-'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8)),
        'school_type' => \App\Domains\School\Enums\SchoolType::Secondary,
        'status' => $statusEnum,
        'settings' => null,
        'metadata' => null,
    ]);
}

function ensureRole(string $name, string $guard = 'web'): Role
{
    return Role::query()->firstOrCreate(['name' => $name, 'guard_name' => $guard]);
}

function createPlatformActor(): User
{
    ensureRole('super_admin');
    $actor = User::factory()->create([
        'school_id' => null,
        'status' => UserStatus::Active->value,
    ]);
    $actor->assignRole('super_admin');

    return $actor;
}

function createSchoolAdminActor(School $school): User
{
    ensureRole('school_admin');
    $actor = User::factory()->create([
        'school_id' => $school->getKey(),
        'status' => UserStatus::Active->value,
    ]);
    $actor->assignRole('school_admin');

    return $actor;
}

it('provisions a school admin account for a platform super admin', function (): void {
    $school = createProvisioningSchool('active');
    $actor = createPlatformActor();
    ensureRole('school_admin');

    $service = new AccountProvisioningService();

    $result = $service->provision(
        new AccountProvisioningCommand(
            schoolId: (string) $school->getKey(),
            firstName: 'Ada',
            lastName: 'Admin',
            email: 'ada.admin@example.test',
            roleName: 'school_admin',
            phone: '+237600000001',
        ),
        AccountProvisioningContext::forPlatform($actor),
    );

    expect($result)->toBeInstanceOf(AccountProvisioningResult::class)
        ->and($result->user)->toBeInstanceOf(User::class)
        ->and($result->user->status)->toBe(UserStatus::Pending)
        ->and((string) $result->user->school_id)->toBe((string) $school->getKey())
        ->and($result->user->email)->toBe('ada.admin@example.test')
        ->and($result->user->hasRole('school_admin'))->toBeTrue();

    $invitation = $result->invitation();
    expect($invitation)->toBeInstanceOf(UserInvitation::class)
        ->and((string) $invitation->school_id)->toBe((string) $school->getKey())
        ->and($invitation->user_id)->toBe($result->user->id)
        ->and($invitation->purpose)->toBe(UserInvitation::PURPOSE_SCHOOL_ADMIN_INITIAL)
        ->and($invitation->isUsable())->toBeTrue();
});

it('provisions an allowed staff role for a school admin within their own school', function (): void {
    $school = createProvisioningSchool('active');
    $actor = createSchoolAdminActor($school);
    ensureRole('teacher');

    $service = new AccountProvisioningService();

    $result = $service->provision(
        new AccountProvisioningCommand(
            schoolId: (string) $school->getKey(),
            firstName: 'Terry',
            lastName: 'Teacher',
            email: 'terry.teacher@example.test',
            roleName: 'teacher',
        ),
        AccountProvisioningContext::forSchoolAdmin($actor),
    );

    expect($result->user->status)->toBe(UserStatus::Pending)
        ->and((string) $result->user->school_id)->toBe((string) $school->getKey())
        ->and($result->user->hasRole('teacher'))->toBeTrue()
        ->and($result->invitation()->purpose)->toBe(UserInvitation::PURPOSE_STAFF_INITIAL)
        ->and((string) $result->invitation()->school_id)->toBe((string) $school->getKey());
});

it('rejects provisioning when email already exists globally', function (): void {
    $school = createProvisioningSchool('active');
    $actor = createPlatformActor();
    ensureRole('school_admin');
    User::factory()->create(['email' => 'taken@example.test']);

    $service = new AccountProvisioningService();

    expect(fn () => $service->provision(
        new AccountProvisioningCommand(
            schoolId: (string) $school->getKey(),
            firstName: 'Taken',
            lastName: 'Email',
            email: 'taken@example.test',
            roleName: 'school_admin',
        ),
        AccountProvisioningContext::forPlatform($actor),
    ))->toThrow(AccountProvisioningException::class, 'email already exists');
});

it('rejects provisioning for nonexistent or inactive schools', function (): void {
    $actor = createPlatformActor();
    ensureRole('school_admin');
    $service = new AccountProvisioningService();

    expect(fn () => $service->provision(
        new AccountProvisioningCommand(
            schoolId: '01NONEXISTENT0000000000000',
            firstName: 'No',
            lastName: 'School',
            email: 'no.school@example.test',
            roleName: 'school_admin',
        ),
        AccountProvisioningContext::forPlatform($actor),
    ))->toThrow(AccountProvisioningException::class, 'does not exist');

    $inactiveSchool = createProvisioningSchool('inactive');
    expect(fn () => $service->provision(
        new AccountProvisioningCommand(
            schoolId: (string) $inactiveSchool->getKey(),
            firstName: 'Inactive',
            lastName: 'School',
            email: 'inactive.school@example.test',
            roleName: 'school_admin',
        ),
        AccountProvisioningContext::forPlatform($actor),
    ))->toThrow(AccountProvisioningException::class, 'must be active');
});

it('prevents school admin from provisioning into another school', function (): void {
    $schoolA = createProvisioningSchool('active');
    $schoolB = createProvisioningSchool('active');
    $actor = createSchoolAdminActor($schoolA);
    ensureRole('teacher');

    $service = new AccountProvisioningService();

    expect(fn () => $service->provision(
        new AccountProvisioningCommand(
            schoolId: (string) $schoolB->getKey(),
            firstName: 'Other',
            lastName: 'School',
            email: 'other.school@example.test',
            roleName: 'teacher',
        ),
        AccountProvisioningContext::forSchoolAdmin($actor),
    ))->toThrow(AccountProvisioningException::class, 'their own school');
});

it('prevents school admin from provisioning forbidden roles', function (): void {
    $school = createProvisioningSchool('active');
    $actor = createSchoolAdminActor($school);
    ensureRole('school_admin');
    ensureRole('director');

    $service = new AccountProvisioningService();

    expect(fn () => $service->provision(
        new AccountProvisioningCommand(
            schoolId: (string) $school->getKey(),
            firstName: 'Forbidden',
            lastName: 'Admin',
            email: 'forbidden.admin@example.test',
            roleName: 'school_admin',
        ),
        AccountProvisioningContext::forSchoolAdmin($actor),
    ))->toThrow(AccountProvisioningException::class, 'cannot provision school_admin');

    expect(fn () => $service->provision(
        new AccountProvisioningCommand(
            schoolId: (string) $school->getKey(),
            firstName: 'Forbidden',
            lastName: 'Director',
            email: 'forbidden.director@example.test',
            roleName: 'director',
        ),
        AccountProvisioningContext::forSchoolAdmin($actor),
    ))->toThrow(AccountProvisioningException::class, 'not allowed for school-admin provisioning');
});

it('prevents school-level provisioning from assigning super_admin', function (): void {
    $school = createProvisioningSchool('active');
    $actor = createSchoolAdminActor($school);
    ensureRole('super_admin');

    $service = new AccountProvisioningService();

    expect(fn () => $service->provision(
        new AccountProvisioningCommand(
            schoolId: (string) $school->getKey(),
            firstName: 'Super',
            lastName: 'Attempt',
            email: 'super.attempt@example.test',
            roleName: 'super_admin',
        ),
        AccountProvisioningContext::forSchoolAdmin($actor),
    ))->toThrow(AccountProvisioningException::class, 'super_admin');
});

it('rolls back provisioning when invitation issuance fails', function (): void {
    $school = createProvisioningSchool('active');
    $actor = createPlatformActor();
    ensureRole('school_admin');

    $service = new AccountProvisioningService();

    UserInvitation::creating(function (): void {
        throw new RuntimeException('Invitation failure');
    });

    try {
        $service->provision(
            new AccountProvisioningCommand(
                schoolId: (string) $school->getKey(),
                firstName: 'Rollback',
                lastName: 'Invitation',
                email: 'rollback.invitation@example.test',
                roleName: 'school_admin',
            ),
            AccountProvisioningContext::forPlatform($actor),
        );
        test()->fail('Expected provisioning to fail.');
    } catch (RuntimeException $e) {
        expect($e->getMessage())->toBe('Invitation failure');
    } finally {
        UserInvitation::clearBootedModels();
    }

    expect(User::withTrashed()->where('email', 'rollback.invitation@example.test')->exists())->toBeFalse()
        ->and(UserInvitation::query()->count())->toBe(0);
});

it('rolls back provisioning when role assignment fails', function (): void {
    $school = createProvisioningSchool('active');
    $actor = createPlatformActor();
    ensureRole('school_admin');

    $service = new AccountProvisioningService();

    DB::listen(function ($query): void {
        $sql = strtolower($query->sql);
        if (str_contains($sql, 'insert') && str_contains($sql, 'model_has_roles')) {
            throw new RuntimeException('Role assignment failure');
        }
    });

    try {
        $service->provision(
            new AccountProvisioningCommand(
                schoolId: (string) $school->getKey(),
                firstName: 'Rollback',
                lastName: 'Role',
                email: 'rollback.role@example.test',
                roleName: 'school_admin',
            ),
            AccountProvisioningContext::forPlatform($actor),
        );
        test()->fail('Expected provisioning to fail.');
    } catch (RuntimeException $e) {
        expect($e->getMessage())->toBe('Role assignment failure');
    } finally {
        DB::getEventDispatcher()?->forget('Illuminate\Database\Events\QueryExecuted');
    }

    expect(User::withTrashed()->where('email', 'rollback.role@example.test')->exists())->toBeFalse()
        ->and(UserInvitation::query()->count())->toBe(0);
});

it('does not persist or expose plaintext invitation token through model serialization or debug output', function (): void {
    $school = createProvisioningSchool('active');
    $actor = createPlatformActor();
    ensureRole('school_admin');

    $service = new AccountProvisioningService();

    $result = $service->provision(
        new AccountProvisioningCommand(
            schoolId: (string) $school->getKey(),
            firstName: 'Token',
            lastName: 'Safety',
            email: 'token.safety@example.test',
            roleName: 'school_admin',
        ),
        AccountProvisioningContext::forPlatform($actor),
    );

    $plainToken = $result->invitationIssue->plainToken;
    $freshInvitation = $result->invitation()->fresh();

    expect($plainToken)->toBeString()->toHaveLength(64)
        ->and($freshInvitation->getAttributes())->not->toHaveKey('plain_token')
        ->and($freshInvitation->toArray())->not->toHaveKey('plain_token')
        ->and($freshInvitation->toArray())->not->toHaveKey('token_hash')
        ->and(json_encode($freshInvitation))->not->toContain($plainToken);

    $debug = $result->__debugInfo();
    expect($debug['plainToken'])->toBe('[redacted]')
        ->and(print_r($result, true))->not->toContain($plainToken);
});
