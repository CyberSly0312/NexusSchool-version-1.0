<?php

declare(strict_types=1);

use App\Domains\Api\Services\ApiTokenService;
use App\Domains\Api\Services\OAuthService;
use App\Domains\School\DTOs\SchoolAdminLifecycleCommand;
use App\Domains\School\DTOs\SchoolAdminReplacementCommand;
use App\Domains\School\Enums\SchoolAdminLifecycleFailure;
use App\Domains\School\Exceptions\SchoolAdminLifecycleException;
use App\Domains\School\Models\School;
use App\Domains\School\Models\SchoolAdminSetupToken;
use App\Domains\School\Services\SchoolAdminLifecycleService;
use App\Domains\System\Enums\AuditAction;
use App\Domains\User\Enums\UserStatus;
use App\Models\ApiClient;
use App\Models\ApiToken;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->schoolId = makeTenant('Lifecycle School', 'active', 'lifecycle.nexusschool.test');
    useTenantHost($this->schoolId);

    foreach (['super_admin', 'school_admin', 'teacher'] as $roleName) {
        Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
    }

    $this->school = School::query()->findOrFail($this->schoolId);
    $this->superAdmin = makeRoleUser($this->schoolId, 'super_admin');
    $this->admin = makeRoleUser($this->schoolId, 'school_admin');
    $this->admin->update(['password' => Hash::make('AncienMotDePasse1!')]);
});

function lifecycleSessionFor(User $user, string $id): void
{
    DB::table('sessions')->insert([
        'id' => $id,
        'user_id' => $user->getKey(),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test',
        'payload' => 'payload',
        'last_activity' => time(),
    ]);
}

function secondAdminFor(string $schoolId): User
{
    return makeRoleUser($schoolId, 'school_admin');
}

test('suspending a former school admin preserves history and revokes every access mechanism', function (): void {
    secondAdminFor($this->schoolId);
    lifecycleSessionFor($this->admin, 'lifecycle-session');

    $invitation = UserInvitation::issue($this->admin, UserInvitation::PURPOSE_SCHOOL_ADMIN_INITIAL, $this->superAdmin);
    $setupToken = SchoolAdminSetupToken::create([
        'school_id' => $this->schoolId,
        'user_id' => $this->admin->getKey(),
        'token_hash' => hash('sha256', 'setup-token'),
        'expires_at' => now()->addDay(),
        'created_by' => $this->superAdmin->getKey(),
    ]);

    $client = ApiClient::factory()->forSchool($this->schoolId)->create();
    $apiToken = app(ApiTokenService::class)->create($client, [
        'name' => 'Jeton sortant',
        'user_id' => $this->admin->getKey(),
        'abilities' => ['*'],
        'expires_at' => now()->addDay()->toDateTimeString(),
    ]);

    $result = app(SchoolAdminLifecycleService::class)->suspend(new SchoolAdminLifecycleCommand(
        $this->superAdmin,
        $this->school,
        (string) $this->admin->getKey(),
    ));

    expect($result->formerAdmin->status)->toBe(UserStatus::Suspended)
        ->and(User::withTrashed()->find($this->admin->getKey()))->not->toBeNull()
        ->and($this->admin->fresh()->hasRole('school_admin'))->toBeTrue()
        ->and(DB::table('sessions')->where('id', 'lifecycle-session')->exists())->toBeFalse()
        ->and($this->admin->fresh()->remember_token)->toBeNull()
        ->and(UserInvitation::find($invitation->invitation->getKey())->isRevoked())->toBeTrue()
        ->and($setupToken->fresh()->isUsed())->toBeTrue()
        ->and(ApiToken::withTrashed()->findOrFail($apiToken->id)->trashed())->toBeTrue()
        ->and(AuditLog::query()->where('action', AuditAction::AccessSuspended->value)->count())->toBe(1);
});

test('the last active school admin cannot be suspended without a replacement', function (): void {
    try {
        app(SchoolAdminLifecycleService::class)->suspend(new SchoolAdminLifecycleCommand(
            $this->superAdmin,
            $this->school,
            (string) $this->admin->getKey(),
        ));
        $this->fail('Le dernier administrateur actif ne doit pas être suspendu.');
    } catch (SchoolAdminLifecycleException $exception) {
        expect($exception->failure)->toBe(SchoolAdminLifecycleFailure::LastActive);
    }

    expect($this->admin->fresh()->status)->toBe(UserStatus::Active)
        ->and(AuditLog::query()->where('action', AuditAction::SensitiveOperationRejected->value)->count())->toBe(1);
});

test('replacement provisions a distinct account and suspends the former admin', function (): void {
    $formerHash = (string) $this->admin->password;

    $result = app(SchoolAdminLifecycleService::class)->replace(new SchoolAdminReplacementCommand(
        actor: $this->superAdmin,
        school: $this->school,
        targetUserId: (string) $this->admin->getKey(),
        firstName: 'Nouvelle',
        lastName: 'Responsable',
        email: 'remplacement@lifecycle.test',
        phone: '+33123456789',
    ));

    $replacement = $result->replacement->fresh();

    expect($result->formerAdmin->status)->toBe(UserStatus::Suspended)
        ->and($replacement->status)->toBe(UserStatus::Pending)
        ->and($replacement->hasRole('school_admin'))->toBeTrue()
        ->and((string) $replacement->getKey())->not->toBe((string) $this->admin->getKey())
        ->and($replacement->email)->not->toBe($this->admin->email)
        ->and($replacement->password)->not->toBe($formerHash)
        ->and(UserInvitation::query()->where('user_id', $replacement->getKey())->count())->toBe(1)
        ->and($result->activationUrl)->toContain('lifecycle.nexusschool.test')
        ->and(AuditLog::query()->where('action', AuditAction::SchoolAdminReplaced->value)->count())->toBe(1)
        ->and(AuditLog::query()->where('action', AuditAction::AccessSuspended->value)->count())->toBe(1);

    $serialized = AuditLog::query()
        ->whereIn('action', [AuditAction::SchoolAdminReplaced->value, AuditAction::AccessSuspended->value])
        ->get()
        ->toJson();

    expect($serialized)->not->toContain('remplacement@lifecycle.test')
        ->and($serialized)->not->toContain('AncienMotDePasse1!');
});

test('replacement cannot transfer the former identity by reusing the email', function (): void {
    try {
        app(SchoolAdminLifecycleService::class)->replace(new SchoolAdminReplacementCommand(
            actor: $this->superAdmin,
            school: $this->school,
            targetUserId: (string) $this->admin->getKey(),
            firstName: 'Autre',
            lastName: 'Identite',
            email: strtoupper((string) $this->admin->email),
        ));
        $this->fail('Le remplacement ne doit pas réutiliser l’identité sortante.');
    } catch (SchoolAdminLifecycleException $exception) {
        expect($exception->failure)->toBe(SchoolAdminLifecycleFailure::IdentityTransfer);
    }

    expect(User::query()->count())->toBe(2)
        ->and($this->admin->fresh()->status)->toBe(UserStatus::Active)
        ->and(AuditLog::query()->where('action', AuditAction::SensitiveOperationRejected->value)->count())->toBe(1);
});

test('a non super admin cannot suspend a school admin', function (): void {
    secondAdminFor($this->schoolId);
    $teacher = makeRoleUser($this->schoolId, 'teacher');

    try {
        app(SchoolAdminLifecycleService::class)->suspend(new SchoolAdminLifecycleCommand(
            $teacher,
            $this->school,
            (string) $this->admin->getKey(),
        ));
        $this->fail('Un enseignant ne doit pas pouvoir suspendre un administrateur.');
    } catch (SchoolAdminLifecycleException $exception) {
        expect($exception->failure)->toBe(SchoolAdminLifecycleFailure::Actor);
    }

    expect($this->admin->fresh()->status)->toBe(UserStatus::Active)
        ->and(AuditLog::query()->where('action', AuditAction::SensitiveOperationRejected->value)->count())->toBe(1);
});

test('lifecycle operations are tenant isolated', function (): void {
    $otherId = makeTenant('Other Lifecycle School', 'active', 'lifecycle-other.nexusschool.test');
    makeRoleUser($otherId, 'school_admin');
    $otherAdmin = User::query()->where('school_id', $otherId)->firstOrFail();

    try {
        app(SchoolAdminLifecycleService::class)->suspend(new SchoolAdminLifecycleCommand(
            $this->superAdmin,
            $this->school,
            (string) $otherAdmin->getKey(),
        ));
        $this->fail('Un administrateur d’une autre école ne doit pas être ciblé.');
    } catch (SchoolAdminLifecycleException $exception) {
        expect($exception->failure)->toBe(SchoolAdminLifecycleFailure::Target);
    }

    expect($otherAdmin->fresh()->status)->toBe(UserStatus::Active)
        ->and(AuditLog::query()->where('school_id', $this->schoolId)->count())->toBeGreaterThan(0)
        ->and(AuditLog::query()->where('school_id', $this->schoolId)->where('auditable_id', $otherAdmin->getKey())->count())->toBe(0);
});

test('a suspended former admin can no longer authenticate or enter a panel', function (): void {
    secondAdminFor($this->schoolId);

    app(SchoolAdminLifecycleService::class)->suspend(new SchoolAdminLifecycleCommand(
        $this->superAdmin,
        $this->school,
        (string) $this->admin->getKey(),
    ));

    RateLimiter::clear('school-gateway-login:ip:127.0.0.1');
    $host = tenantHost($this->schoolId);

    $this->post("http://{$host}/login", [
        'email' => $this->admin->email,
        'password' => 'AncienMotDePasse1!',
    ])->assertSessionHasErrors('email');

    expect(Auth::guard('web')->check())->toBeFalse();

    $this->actingAs($this->admin->fresh())
        ->get("http://{$host}/school-admin")
        ->assertForbidden();
});

test('api tokens of a suspended admin are rejected', function (): void {
    secondAdminFor($this->schoolId);

    $client = ApiClient::factory()->forSchool($this->schoolId)->create();
    $apiToken = app(ApiTokenService::class)->create($client, [
        'name' => 'Jeton à révoquer',
        'user_id' => $this->admin->getKey(),
        'abilities' => ['*'],
        'expires_at' => now()->addDay()->toDateTimeString(),
    ]);

    expect(app(OAuthService::class)->validateToken($apiToken->token))->not->toBeNull();

    app(SchoolAdminLifecycleService::class)->suspend(new SchoolAdminLifecycleCommand(
        $this->superAdmin,
        $this->school,
        (string) $this->admin->getKey(),
    ));

    expect(app(OAuthService::class)->validateToken($apiToken->token))->toBeNull();
});
