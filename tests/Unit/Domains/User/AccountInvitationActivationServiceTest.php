<?php

declare(strict_types=1);

use App\Domains\School\Models\School;
use App\Domains\User\DTOs\AccountInvitationActivationResult;
use App\Domains\User\Enums\UserStatus;
use App\Domains\User\Exceptions\AccountInvitationActivationException;
use App\Domains\User\Services\AccountInvitationActivationService;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

function createActivationSchool(string $status = 'active'): School
{
    $statusEnum = match ($status) {
        'active' => \App\Domains\School\Enums\SchoolStatus::Active,
        'inactive' => \App\Domains\School\Enums\SchoolStatus::Inactive,
        'pending' => \App\Domains\School\Enums\SchoolStatus::Pending,
        'suspended' => \App\Domains\School\Enums\SchoolStatus::Suspended,
        default => throw new InvalidArgumentException("Unknown status [{$status}]"),
    };

    $name = 'Activation School '.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6));

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

function createPendingInvitedUser(School $school, array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'school_id' => $school->getKey(),
        'status' => UserStatus::Pending->value,
        'password' => Hash::make('old-password'),
        'email_verified_at' => null,
    ], $attributes));
}

it('activates a pending user with a valid invitation', function (): void {
    $school = createActivationSchool();
    $user = createPendingInvitedUser($school);
    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $service = new AccountInvitationActivationService();

    $result = $service->activate($issue->plainToken, 'NewSecurePassword123!');

    expect($result)->toBeInstanceOf(AccountInvitationActivationResult::class)
        ->and($result->user)->toBeInstanceOf(User::class)
        ->and($result->user->id)->toBe($user->id)
        ->and($result->user->status)->toBe(UserStatus::Active)
        ->and(Hash::check('NewSecurePassword123!', $result->user->password))->toBeTrue()
        ->and($result->user->email_verified_at)->toBeNull();

    $invitation = $issue->invitation->fresh();
    expect($invitation->isConsumed())->toBeTrue()
        ->and($invitation->isUsable())->toBeFalse();
});

it('does not store plaintext password and hashes the new password', function (): void {
    $school = createActivationSchool();
    $user = createPendingInvitedUser($school);
    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_SCHOOL_ADMIN_INITIAL);
    $plainPassword = 'AnotherSecurePassword123!';
    $service = new AccountInvitationActivationService();

    $service->activate($issue->plainToken, $plainPassword);

    $freshUser = $user->fresh();
    expect($freshUser->password)->not->toBe($plainPassword)
        ->and(Hash::check($plainPassword, $freshUser->password))->toBeTrue();
});

it('cannot reuse an invitation after successful activation', function (): void {
    $school = createActivationSchool();
    $user = createPendingInvitedUser($school);
    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $service = new AccountInvitationActivationService();

    $service->activate($issue->plainToken, 'NewSecurePassword123!');

    expect(fn () => $service->activate($issue->plainToken, 'AnotherPassword123!'))
        ->toThrow(AccountInvitationActivationException::class);

    expect($user->fresh()->status)->toBe(UserStatus::Active)
        ->and(Hash::check('NewSecurePassword123!', $user->fresh()->password))->toBeTrue();
});

it('rejects invalid token', function (): void {
    $service = new AccountInvitationActivationService();

    expect(fn () => $service->activate('not-a-real-token', 'NewSecurePassword123!'))
        ->toThrow(AccountInvitationActivationException::class, 'invalid, expired, or has already been used');
});

it('rejects expired invitation and leaves user unchanged', function (): void {
    $school = createActivationSchool();
    $user = createPendingInvitedUser($school);
    $invitation = UserInvitation::factory()->expired()->create([
        'user_id' => $user->id,
        'school_id' => $school->getKey(),
        'purpose' => UserInvitation::PURPOSE_STAFF_INITIAL,
        'token_hash' => UserInvitation::hashToken('expired-token'),
    ]);
    $service = new AccountInvitationActivationService();

    expect(fn () => $service->activate('expired-token', 'NewSecurePassword123!'))
        ->toThrow(AccountInvitationActivationException::class);

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($invitation->fresh()->isConsumed())->toBeFalse();
});

it('rejects revoked invitation and leaves user unchanged', function (): void {
    $school = createActivationSchool();
    $user = createPendingInvitedUser($school);
    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $issue->invitation->revoke();
    $service = new AccountInvitationActivationService();

    expect(fn () => $service->activate($issue->plainToken, 'NewSecurePassword123!'))
        ->toThrow(AccountInvitationActivationException::class);

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($issue->invitation->fresh()->isConsumed())->toBeFalse()
        ->and($issue->invitation->fresh()->isRevoked())->toBeTrue();
});

it('rejects already consumed invitation', function (): void {
    $school = createActivationSchool();
    $user = createPendingInvitedUser($school);
    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $issue->invitation->consume();
    $service = new AccountInvitationActivationService();

    expect(fn () => $service->activate($issue->plainToken, 'NewSecurePassword123!'))
        ->toThrow(AccountInvitationActivationException::class);

    expect($user->fresh()->status)->toBe(UserStatus::Pending);
});

it('rejects already active, inactive, or suspended users', function (string $status): void {
    $school = createActivationSchool();
    $user = createPendingInvitedUser($school, ['status' => $status]);
    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $service = new AccountInvitationActivationService();

    expect(fn () => $service->activate($issue->plainToken, 'NewSecurePassword123!'))
        ->toThrow(AccountInvitationActivationException::class);

    expect($user->fresh()->status->value)->toBe($status)
        ->and($issue->invitation->fresh()->isConsumed())->toBeFalse();
})->with([
    UserStatus::Active->value,
    UserStatus::Inactive->value,
    UserStatus::Suspended->value,
]);

it('rejects invitation with missing associated user', function (): void {
    $school = createActivationSchool();
    $user = createPendingInvitedUser($school);
    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);

    $user->delete();

    $service = new AccountInvitationActivationService();

    expect(fn () => $service->activate($issue->plainToken, 'NewSecurePassword123!'))
        ->toThrow(AccountInvitationActivationException::class);

    expect($issue->invitation->fresh()->isConsumed())->toBeFalse();
});

it('rejects missing or mismatched school', function (): void {
    $schoolA = createActivationSchool();
    $schoolB = createActivationSchool();
    $user = createPendingInvitedUser($schoolA);
    $plainToken = bin2hex(random_bytes(32));

    UserInvitation::query()->insert([
        'id' => (string) \Illuminate\Support\Str::ulid(),
        'user_id' => $user->id,
        'school_id' => (string) $schoolB->getKey(),
        'purpose' => UserInvitation::PURPOSE_STAFF_INITIAL,
        'token_hash' => UserInvitation::hashToken($plainToken),
        'expires_at' => now()->addHour(),
        'consumed_at' => null,
        'revoked_at' => null,
        'invited_by_user_id' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $service = new AccountInvitationActivationService();

    expect(fn () => $service->activate($plainToken, 'NewSecurePassword123!'))
        ->toThrow(AccountInvitationActivationException::class);

    expect($user->fresh()->status)->toBe(UserStatus::Pending);

    $schoolC = createActivationSchool();
    $user2 = createPendingInvitedUser($schoolC);
    $issue2 = UserInvitation::issue($user2, UserInvitation::PURPOSE_STAFF_INITIAL);
    $schoolC->delete();

    expect(fn () => $service->activate($issue2->plainToken, 'NewSecurePassword123!'))
        ->toThrow(AccountInvitationActivationException::class);

    expect($user2->fresh()->status)->toBe(UserStatus::Pending)
        ->and($issue2->invitation->fresh()->isConsumed())->toBeFalse();
});

it('enforces the expected school context inside the activation transaction', function (): void {
    $schoolA = createActivationSchool();
    $schoolB = createActivationSchool();
    $user = createPendingInvitedUser($schoolA);
    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $service = new AccountInvitationActivationService();

    expect(fn () => $service->activate($issue->plainToken, 'NewSecurePassword123!', $schoolB))
        ->toThrow(AccountInvitationActivationException::class);

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($issue->invitation->fresh()->isConsumed())->toBeFalse();

    $result = $service->activate($issue->plainToken, 'NewSecurePassword123!', $schoolA);

    expect($result->user->status)->toBe(UserStatus::Active)
        ->and($issue->invitation->fresh()->isConsumed())->toBeTrue();
});

it('finds a usable invitation only for its own school', function (): void {
    $schoolA = createActivationSchool();
    $schoolB = createActivationSchool();
    $user = createPendingInvitedUser($schoolA);
    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $service = new AccountInvitationActivationService();

    expect($service->findUsableForSchool($issue->plainToken, $schoolA)?->getKey())
        ->toBe($issue->invitation->getKey())
        ->and($service->findUsableForSchool($issue->plainToken, $schoolB))->toBeNull();
});

it('rejects invalid invitation purpose', function (): void {
    $school = createActivationSchool();
    $user = createPendingInvitedUser($school);
    $plainToken = bin2hex(random_bytes(32));

    UserInvitation::query()->insert([
        'id' => (string) \Illuminate\Support\Str::ulid(),
        'user_id' => $user->id,
        'school_id' => (string) $school->getKey(),
        'purpose' => 'unknown_purpose',
        'token_hash' => UserInvitation::hashToken($plainToken),
        'expires_at' => now()->addHour(),
        'consumed_at' => null,
        'revoked_at' => null,
        'invited_by_user_id' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $service = new AccountInvitationActivationService();

    expect(fn () => $service->activate($plainToken, 'NewSecurePassword123!'))
        ->toThrow(AccountInvitationActivationException::class);

    expect($user->fresh()->status)->toBe(UserStatus::Pending);
});

it('rejects weak password and leaves invitation and user unchanged', function (): void {
    $school = createActivationSchool();
    $user = createPendingInvitedUser($school);
    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $oldPassword = $user->password;
    $service = new AccountInvitationActivationService();

    expect(fn () => $service->activate($issue->plainToken, 'short'))
        ->toThrow(ValidationException::class);

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($user->fresh()->password)->toBe($oldPassword)
        ->and($issue->invitation->fresh()->isConsumed())->toBeFalse();
});

it('rolls back if user update fails', function (): void {
    $school = createActivationSchool();
    $user = createPendingInvitedUser($school);
    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $service = new AccountInvitationActivationService();

    User::saving(function (User $savingUser) use ($user): void {
        if ((string) $savingUser->getKey() === (string) $user->getKey() && $savingUser->status === UserStatus::Active) {
            throw new RuntimeException('User update failure');
        }
    });

    try {
        $service->activate($issue->plainToken, 'NewSecurePassword123!');
        test()->fail('Expected activation to fail.');
    } catch (RuntimeException $e) {
        expect($e->getMessage())->toBe('User update failure');
    } finally {
        User::clearBootedModels();
    }

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($issue->invitation->fresh()->isConsumed())->toBeFalse();
});

it('rolls back if invitation consumption fails', function (): void {
    $school = createActivationSchool();
    $user = createPendingInvitedUser($school);
    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $service = new AccountInvitationActivationService();

    UserInvitation::updating(function (UserInvitation $savingInvitation) use ($issue): void {
        if ((string) $savingInvitation->getKey() === (string) $issue->invitation->getKey() && $savingInvitation->consumed_at !== null) {
            throw new RuntimeException('Invitation consumption failure');
        }
    });

    try {
        $service->activate($issue->plainToken, 'NewSecurePassword123!');
        test()->fail('Expected activation to fail.');
    } catch (RuntimeException $e) {
        expect($e->getMessage())->toBe('Invitation consumption failure');
    } finally {
        UserInvitation::clearBootedModels();
    }

    expect($user->fresh()->status)->toBe(UserStatus::Pending)
        ->and($issue->invitation->fresh()->isConsumed())->toBeFalse();
});
