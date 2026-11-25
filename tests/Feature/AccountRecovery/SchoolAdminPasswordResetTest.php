<?php

declare(strict_types=1);

use App\Domains\AccountRecovery\DTOs\CompletePasswordResetCommand;
use App\Domains\AccountRecovery\DTOs\SchoolAdminResetCommand;
use App\Domains\AccountRecovery\Enums\PasswordResetFailure;
use App\Domains\AccountRecovery\Exceptions\PasswordResetException;
use App\Domains\AccountRecovery\Services\SchoolAdminPasswordResetService;
use App\Domains\Api\Services\ApiTokenService;
use App\Domains\School\Models\School;
use App\Domains\System\Enums\AuditAction;
use App\Domains\User\Enums\UserStatus;
use App\Models\ApiClient;
use App\Models\ApiToken;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->schoolId = makeTenant('Reset School', 'active', 'reset-school.nexusschool.test');
    useTenantHost($this->schoolId);

    foreach (['super_admin', 'school_admin', 'teacher'] as $roleName) {
        Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
    }

    $this->school = School::query()->findOrFail($this->schoolId);
    $this->superAdmin = makeRoleUser($this->schoolId, 'super_admin');
    $this->admin = makeRoleUser($this->schoolId, 'school_admin');
    $this->admin->update([
        'password' => Hash::make('AncienMotDePasse1!'),
        'status' => UserStatus::Active,
    ]);
});

function issueResetFor(User $actor, School $school, User $target)
{
    return app(SchoolAdminPasswordResetService::class)->initiate(
        new SchoolAdminResetCommand($actor, $school, (string) $target->getKey()),
    );
}

function completeResetFor(School $school, string $token, string $password = 'MotDePasseFort4!')
{
    return app(SchoolAdminPasswordResetService::class)->complete(new CompletePasswordResetCommand(
        school: $school,
        token: $token,
        password: $password,
        passwordConfirmation: $password,
    ));
}

test('a super admin initiates a hashed single-use reset without choosing a password', function (): void {
    $result = issueResetFor($this->superAdmin, $this->school, $this->admin);

    $invitation = UserInvitation::findByPlainToken($result->plainToken);

    expect($invitation)->not->toBeNull()
        ->and($invitation->purpose)->toBe(UserInvitation::PURPOSE_SCHOOL_ADMIN_RESET)
        ->and($invitation->token_hash)->toBe(hash('sha256', $result->plainToken))
        ->and($invitation->isUsable())->toBeTrue()
        ->and(abs($invitation->expires_at->diffInHours(now())))->toBeGreaterThan(23.9)
        ->and(abs($invitation->expires_at->diffInHours(now())))->toBeLessThanOrEqual(24)
        ->and($result->resetUrl)->toContain('reset-school.nexusschool.test')
        ->and($result->resetUrl)->toContain($result->plainToken)
        ->and(AuditLog::query()->where('action', AuditAction::PasswordResetRequested->value)->count())->toBe(1);

    $audit = AuditLog::query()->where('action', AuditAction::PasswordResetRequested->value)->firstOrFail();
    $serialized = json_encode($audit->toArray(), JSON_THROW_ON_ERROR);

    expect($serialized)->not->toContain($result->plainToken)
        ->and($serialized)->not->toContain((string) $this->admin->password);
});

test('a non super admin actor is denied and the rejection is audited', function (): void {
    $teacher = makeRoleUser($this->schoolId, 'teacher');

    try {
        issueResetFor($teacher, $this->school, $this->admin);
        $this->fail('Un enseignant ne doit pas pouvoir demander une réinitialisation.');
    } catch (PasswordResetException $exception) {
        expect($exception->failure)->toBe(PasswordResetFailure::Actor);
    }

    expect(AuditLog::query()->where('action', AuditAction::PasswordResetRejected->value)->count())->toBe(1)
        ->and(UserInvitation::query()->count())->toBe(0);
});

test('a reset link expires and cannot be completed after expiry', function (): void {
    $result = issueResetFor($this->superAdmin, $this->school, $this->admin);

    UserInvitation::findByPlainToken($result->plainToken)->update([
        'expires_at' => now()->subMinute(),
    ]);

    try {
        completeResetFor($this->school, $result->plainToken);
        $this->fail('Un lien expiré doit être refusé.');
    } catch (PasswordResetException $exception) {
        expect($exception->failure)->toBe(PasswordResetFailure::TokenExpired);
    }

    expect(Hash::check('AncienMotDePasse1!', $this->admin->fresh()->password))->toBeTrue()
        ->and(AuditLog::query()->where('action', AuditAction::PasswordResetRejected->value)->count())->toBe(1);
});

test('a reset link is single use', function (): void {
    $result = issueResetFor($this->superAdmin, $this->school, $this->admin);

    completeResetFor($this->school, $result->plainToken);

    try {
        completeResetFor($this->school, $result->plainToken);
        $this->fail('Le lien ne doit pas être réutilisable.');
    } catch (PasswordResetException $exception) {
        expect($exception->failure)->toBe(PasswordResetFailure::TokenAlreadyUsed);
    }

    expect(Hash::check('MotDePasseFort4!', $this->admin->fresh()->password))->toBeTrue()
        ->and(AuditLog::query()->where('action', AuditAction::PasswordResetCompleted->value)->count())->toBe(1);
});

test('issuing a new reset revokes the previous outstanding token', function (): void {
    $first = issueResetFor($this->superAdmin, $this->school, $this->admin);
    $second = issueResetFor($this->superAdmin, $this->school, $this->admin);

    expect(UserInvitation::findByPlainToken($first->plainToken)->isRevoked())->toBeTrue()
        ->and(UserInvitation::findByPlainToken($second->plainToken)->isUsable())->toBeTrue();

    try {
        completeResetFor($this->school, $first->plainToken);
        $this->fail('Un lien révoqué doit être refusé.');
    } catch (PasswordResetException $exception) {
        expect($exception->failure)->toBe(PasswordResetFailure::TokenRevoked);
    }

    completeResetFor($this->school, $second->plainToken);

    expect(Hash::check('MotDePasseFort4!', $this->admin->fresh()->password))->toBeTrue();
});

test('completion sets the password chosen by the school admin and revokes access', function (): void {
    $result = issueResetFor($this->superAdmin, $this->school, $this->admin);

    DB::table('sessions')->insert([
        'id' => 'reset-session',
        'user_id' => $this->admin->getKey(),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test',
        'payload' => 'payload',
        'last_activity' => time(),
    ]);
    DB::table('password_reset_tokens')->insert([
        'email' => $this->admin->email,
        'token' => 'legacy-token',
        'created_at' => now(),
    ]);

    $client = ApiClient::factory()->forSchool($this->schoolId)->create();
    $apiToken = app(ApiTokenService::class)->create($client, [
        'name' => 'Jetons de test',
        'user_id' => $this->admin->getKey(),
        'abilities' => ['*'],
        'expires_at' => now()->addDay()->toDateTimeString(),
    ]);

    completeResetFor($this->school, $result->plainToken);

    expect(Hash::check('MotDePasseFort4!', $this->admin->fresh()->password))->toBeTrue()
        ->and(DB::table('sessions')->where('id', 'reset-session')->exists())->toBeFalse()
        ->and(DB::table('password_reset_tokens')->where('email', $this->admin->email)->exists())->toBeFalse()
        ->and(ApiToken::withTrashed()->findOrFail($apiToken->id)->trashed())->toBeTrue()
        ->and($this->admin->fresh()->remember_token)->toBeNull();

    $serialized = AuditLog::query()->where('action', AuditAction::PasswordResetCompleted->value)
        ->firstOrFail()
        ->toJson();

    expect($serialized)->not->toContain($result->plainToken)
        ->and($serialized)->not->toContain('MotDePasseFort4!')
        ->and($serialized)->not->toContain((string) $this->admin->password);
});

test('reset completion is tenant bound', function (): void {
    $otherId = makeTenant('Other Reset School', 'active', 'reset-other.nexusschool.test');
    $otherSchool = School::query()->findOrFail($otherId);
    makeRoleUser($otherId, 'school_admin');
    $otherAdmin = User::query()->where('school_id', $otherId)->firstOrFail();

    $originalHash = (string) $otherAdmin->password;

    useTenantHost($otherId);
    $result = issueResetFor($this->superAdmin, $otherSchool, $otherAdmin);
    useTenantHost($this->schoolId);

    try {
        completeResetFor($this->school, $result->plainToken);
        $this->fail('Un lien d’une autre école doit être refusé.');
    } catch (PasswordResetException $exception) {
        expect($exception->failure)->toBe(PasswordResetFailure::Token);
    }

    expect($otherAdmin->fresh()->password)->toBe($originalHash)
        ->and(UserInvitation::findByPlainToken($result->plainToken)->isUsable())->toBeTrue();
});

test('weak passwords are rejected with French validation messages', function (): void {
    $result = issueResetFor($this->superAdmin, $this->school, $this->admin);

    try {
        completeResetFor($this->school, $result->plainToken, 'faible');
        $this->fail('Un mot de passe faible doit être refusé.');
    } catch (PasswordResetException $exception) {
        expect($exception->failure)->toBe(PasswordResetFailure::Validation)
            ->and(implode(' ', $exception->validationMessages))->toContain('12 caractères');
    }

    expect(UserInvitation::findByPlainToken($result->plainToken)->isUsable())->toBeTrue()
        ->and(Hash::check('AncienMotDePasse1!', $this->admin->fresh()->password))->toBeTrue();
});

test('the tenant reset page validates and completes in French without leaking secrets', function (): void {
    $result = issueResetFor($this->superAdmin, $this->school, $this->admin);
    $host = tenantHost($this->schoolId);

    $show = $this->get("http://{$host}/account/password-reset/{$result->plainToken}");
    $show->assertOk()->assertSee('Réinitialiser votre mot de passe');

    $this->post("http://{$host}/account/password-reset/{$result->plainToken}", [
        'password' => 'faible',
        'password_confirmation' => 'faible',
    ])->assertSessionHasErrors('password');

    expect(session('errors')->first('password'))->toContain('12 caractères');

    $this->post("http://{$host}/account/password-reset/{$result->plainToken}", [
        'password' => 'MotDePasseFort4!',
        'password_confirmation' => 'MotDePasseFort4!',
    ])->assertRedirect("http://{$host}/account/password-reset-termine");

    expect(Hash::check('MotDePasseFort4!', $this->admin->fresh()->password))->toBeTrue();

    $done = $this->get("http://{$host}/account/password-reset-termine");
    $done->assertOk()
        ->assertSee('Mot de passe réinitialisé')
        ->assertDontSee($result->plainToken)
        ->assertDontSee('MotDePasseFort4!');
});
