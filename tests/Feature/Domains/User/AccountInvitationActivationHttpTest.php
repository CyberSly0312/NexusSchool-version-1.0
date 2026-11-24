<?php

declare(strict_types=1);

use App\Domains\User\DTOs\UserInvitationIssue;
use App\Domains\User\Enums\UserStatus;
use App\Models\ApiToken;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

/**
 * @return array{0: User, 1: UserInvitationIssue}
 */
function makeActivationFixture(string $schoolId, string $email): array
{
    $user = User::factory()->create([
        'school_id' => $schoolId,
        'email' => $email,
        'status' => UserStatus::Pending->value,
        'password' => Hash::make('old-password'),
        'email_verified_at' => null,
    ]);

    return [$user, UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL)];
}

function activationTestUrl(string $schoolId, string $token): string
{
    return 'http://'.tenantHost($schoolId).'/account/activate/'.$token;
}

test('the activation routes are registered as public tenant-host routes', function (): void {
    $show = Route::getRoutes()->getByName('school.account.activation.show');
    $store = Route::getRoutes()->getByName('school.account.activation.store');

    expect($show)->not->toBeNull()
        ->and($store)->not->toBeNull()
        ->and($show->uri())->toBe('account/activate/{token}')
        ->and($store->uri())->toBe('account/activate/{token}')
        ->and($show->methods())->toContain('GET')
        ->and($store->methods())->toContain('POST');
});

test('a valid invitation activates the account on its own school host without signing in', function (): void {
    $schoolId = makeTenant('Activation School');
    [$user, $issue] = makeActivationFixture($schoolId, 'new.staff@example.test');
    $url = activationTestUrl($schoolId, $issue->plainToken);

    $this->get($url)
        ->assertOk()
        ->assertSee('Activation de votre compte')
        ->assertSee('Choisissez votre mot de passe')
        ->assertSee('name="password"', false)
        ->assertSee('name="password_confirmation"', false)
        ->assertSee('name="_token"', false);
    $this->assertGuest();

    $password = 'ActivatedPassword123!';

    $this->post($url, [
        'password' => $password,
        'password_confirmation' => $password,
    ])
        ->assertOk()
        ->assertSee('Votre compte est activé')
        ->assertSee('Se connecter');

    $freshUser = $user->fresh();

    expect($freshUser->status)->toBe(UserStatus::Active)
        ->and(Hash::check($password, $freshUser->password))->toBeTrue()
        ->and(Hash::check('old-password', $freshUser->password))->toBeFalse()
        ->and($freshUser->email_verified_at)->toBeNull()
        ->and($issue->invitation->fresh()->isConsumed())->toBeTrue()
        ->and(ApiToken::query()->count())->toBe(0);

    $this->assertGuest();
});

test('an invitation cannot be activated through another school host', function (): void {
    $schoolA = makeTenant('School A');
    $schoolB = makeTenant('School B');
    [$user, $issue] = makeActivationFixture($schoolA, 'owned.by.a@example.test');
    $foreignUrl = activationTestUrl($schoolB, $issue->plainToken);

    $this->get($foreignUrl)
        ->assertNotFound()
        ->assertSee('invalide, expiré', false)
        ->assertDontSee($user->email)
        ->assertDontSee('School A');

    $this->post($foreignUrl, [
        'password' => 'ActivatedPassword123!',
        'password_confirmation' => 'ActivatedPassword123!',
    ])
        ->assertNotFound()
        ->assertSee('invalide, expiré', false)
        ->assertDontSee($user->email)
        ->assertDontSee('School A');

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($issue->invitation->fresh()->isConsumed())->toBeFalse();

    $this->assertGuest();
});

test('an invalid token returns a generic failure without changing account state', function (): void {
    $schoolId = makeTenant('Activation School');
    [$user, $issue] = makeActivationFixture($schoolId, 'valid.user@example.test');
    $invalidUrl = activationTestUrl($schoolId, bin2hex(random_bytes(32)));

    $this->get($invalidUrl)
        ->assertNotFound()
        ->assertSee('invalide, expiré', false)
        ->assertDontSee($user->email);

    $this->post($invalidUrl, [
        'password' => 'ActivatedPassword123!',
        'password_confirmation' => 'ActivatedPassword123!',
    ])->assertNotFound();

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($issue->invitation->fresh()->isConsumed())->toBeFalse();
});

test('an expired invitation is rejected and leaves the account pending', function (): void {
    $schoolId = makeTenant('Activation School');
    [$user, $issue] = makeActivationFixture($schoolId, 'expired.user@example.test');
    $issue->invitation->update(['expires_at' => now()->subMinute()]);

    $this->post(activationTestUrl($schoolId, $issue->plainToken), [
        'password' => 'ActivatedPassword123!',
        'password_confirmation' => 'ActivatedPassword123!',
    ])->assertNotFound();

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($issue->invitation->fresh()->isConsumed())->toBeFalse();
});

test('a consumed invitation cannot be reused', function (): void {
    $schoolId = makeTenant('Activation School');
    [$user, $issue] = makeActivationFixture($schoolId, 'consumed.user@example.test');
    $issue->invitation->consume();
    $originalPassword = $user->password;

    $this->post(activationTestUrl($schoolId, $issue->plainToken), [
        'password' => 'AnotherPassword123!',
        'password_confirmation' => 'AnotherPassword123!',
    ])->assertNotFound();

    $freshUser = $user->fresh();

    expect($freshUser->status)->toBe(UserStatus::Pending)
        ->and($freshUser->password)->toBe($originalPassword)
        ->and($issue->invitation->fresh()->isConsumed())->toBeTrue();
});

test('a revoked invitation is rejected', function (): void {
    $schoolId = makeTenant('Activation School');
    [$user, $issue] = makeActivationFixture($schoolId, 'revoked.user@example.test');
    $issue->invitation->revoke();

    $this->post(activationTestUrl($schoolId, $issue->plainToken), [
        'password' => 'ActivatedPassword123!',
        'password_confirmation' => 'ActivatedPassword123!',
    ])->assertNotFound();

    $freshInvitation = $issue->invitation->fresh();

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($freshInvitation->isConsumed())->toBeFalse()
        ->and($freshInvitation->isRevoked())->toBeTrue();
});

test('a password confirmation mismatch leaves the invitation usable', function (): void {
    $schoolId = makeTenant('Activation School');
    [$user, $issue] = makeActivationFixture($schoolId, 'confirmation.user@example.test');
    $url = activationTestUrl($schoolId, $issue->plainToken);

    $this->from($url)->post($url, [
        'password' => 'ActivatedPassword123!',
        'password_confirmation' => 'DifferentPassword123!',
    ])->assertSessionHasErrors('password');

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($issue->invitation->fresh()->isUsable())->toBeTrue();
});

test('a password below eight characters is rejected and leaves the invitation usable', function (): void {
    $schoolId = makeTenant('Activation School');
    [$user, $issue] = makeActivationFixture($schoolId, 'weak.user@example.test');
    $url = activationTestUrl($schoolId, $issue->plainToken);

    $this->from($url)->post($url, [
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertSessionHasErrors('password');

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($issue->invitation->fresh()->isUsable())->toBeTrue();
});

test('the activation page is public and does not redirect guests to login', function (): void {
    $schoolId = makeTenant('Activation School');
    [, $issue] = makeActivationFixture($schoolId, 'public.user@example.test');

    $this->get(activationTestUrl($schoolId, $issue->plainToken))->assertOk();

    $this->assertGuest();
});

test('existing active accounts still sign in through the gateway without an invitation', function (): void {
    $schoolId = makeTenant('Legacy School');
    $host = tenantHost($schoolId);
    $user = User::factory()->create([
        'school_id' => $schoolId,
        'status' => UserStatus::Active->value,
        'password' => Hash::make('password123'),
    ]);
    Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
    $user->assignRole('teacher');

    $this->post("http://{$host}/login", [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertRedirect("http://{$host}/teacher");

    $this->assertAuthenticatedAs($user);

    expect(UserInvitation::query()->where('user_id', $user->getKey())->count())->toBe(0);
});

test('activation is not served on central or unknown hosts', function (): void {
    $schoolId = makeTenant('Activation School');
    [, $issue] = makeActivationFixture($schoolId, 'host.user@example.test');

    $this->get('http://localhost/account/activate/'.$issue->plainToken)->assertNotFound();
    $this->get('http://unknown-school.nexusschool.test/account/activate/'.$issue->plainToken)->assertNotFound();
});

test('activation is denied for inactive and suspended schools', function (string $status): void {
    $schoolId = makeTenant('Status School', $status);
    [$user, $issue] = makeActivationFixture($schoolId, 'status.user@example.test');
    $url = activationTestUrl($schoolId, $issue->plainToken);

    $this->get($url)->assertForbidden();
    $this->post($url, [
        'password' => 'ActivatedPassword123!',
        'password_confirmation' => 'ActivatedPassword123!',
    ])->assertForbidden();

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($issue->invitation->fresh()->isConsumed())->toBeFalse();
})->with([
    'inactive' => ['inactive'],
    'suspended' => ['suspended'],
]);
