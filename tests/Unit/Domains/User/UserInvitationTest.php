<?php

declare(strict_types=1);

use App\Domains\User\DTOs\UserInvitationIssue;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

function createInvitationTenant(string $name = 'Invitation School'): string
{
    return makeTenant($name);
}

function createInvitationUser(string $schoolId, array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'school_id' => $schoolId,
        'status' => 'pending',
    ], $attributes));
}

it('creates an invitation with ulid primary key and expected schema attributes', function (): void {
    $schoolId = createInvitationTenant();
    $user = createInvitationUser($schoolId);

    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);

    expect($issue)->toBeInstanceOf(UserInvitationIssue::class)
        ->and($issue->invitation)->toBeInstanceOf(UserInvitation::class)
        ->and($issue->invitation->id)->toBeString()->not->toBeEmpty()
        ->and(Str::isUlid($issue->invitation->id))->toBeTrue()
        ->and($issue->invitation->user_id)->toBe($user->id)
        ->and($issue->invitation->school_id)->toBe($schoolId)
        ->and($issue->invitation->purpose)->toBe(UserInvitation::PURPOSE_STAFF_INITIAL)
        ->and($issue->invitation->expires_at)->not->toBeNull()
        ->and($issue->invitation->consumed_at)->toBeNull()
        ->and($issue->invitation->revoked_at)->toBeNull();
});

it('stores only a sha256 token hash and never persists plaintext token', function (): void {
    $schoolId = createInvitationTenant();
    $user = createInvitationUser($schoolId);

    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $invitation = $issue->invitation->fresh();

    expect($issue->plainToken)->toBeString()->toHaveLength(64)
        ->and($invitation->token_hash)->toBe(hash('sha256', $issue->plainToken))
        ->and($invitation->token_hash)->not->toBe($issue->plainToken)
        ->and($invitation->getAttributes())->not->toHaveKey('plain_token')
        ->and($invitation->toArray())->not->toHaveKey('plain_token')
        ->and($invitation->toArray())->not->toHaveKey('token_hash')
        ->and(json_encode($invitation))->not->toContain($issue->plainToken);
});

it('redacts plaintext token in issue debug info', function (): void {
    $schoolId = createInvitationTenant();
    $user = createInvitationUser($schoolId);

    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $debug = $issue->__debugInfo();

    expect($debug['plainToken'])->toBe('[redacted]')
        ->and($debug['invitation'])->toBeInstanceOf(UserInvitation::class);
});

it('enforces unique token hash at the database level', function (): void {
    $schoolId = createInvitationTenant();
    $userA = createInvitationUser($schoolId);
    $userB = createInvitationUser($schoolId);
    $hash = hash('sha256', bin2hex(random_bytes(32)));

    UserInvitation::factory()->create([
        'user_id' => $userA->id,
        'school_id' => $schoolId,
        'token_hash' => $hash,
    ]);

    expect(fn () => UserInvitation::factory()->create([
        'user_id' => $userB->id,
        'school_id' => $schoolId,
        'token_hash' => $hash,
    ]))->toThrow(QueryException::class);
});

it('defines user and school relationships', function (): void {
    $schoolId = createInvitationTenant();
    $user = createInvitationUser($schoolId);

    $invitation = UserInvitation::issue($user, UserInvitation::PURPOSE_SCHOOL_ADMIN_INITIAL)->invitation->fresh(['user', 'school']);

    expect($invitation->user)->toBeInstanceOf(User::class)
        ->and($invitation->user->is($user))->toBeTrue()
        ->and($invitation->school)->not->toBeNull()
        ->and((string) $invitation->school->getKey())->toBe((string) $schoolId);
});

it('is expired after 48 hours and not usable once expired', function (): void {
    Carbon::setTestNow('2026-11-24 10:00:00');
    $schoolId = createInvitationTenant();
    $user = createInvitationUser($schoolId);

    $invitation = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL)->invitation->fresh();

    expect($invitation->expires_at->toDateTimeString())->toBe('2026-11-26 10:00:00')
        ->and($invitation->isExpired())->toBeFalse()
        ->and($invitation->isUsable())->toBeTrue();

    Carbon::setTestNow('2026-11-26 10:00:01');

    expect($invitation->isExpired())->toBeTrue()
        ->and($invitation->isUsable())->toBeFalse();

    Carbon::setTestNow();
});

it('consumes an invitation only once', function (): void {
    $schoolId = createInvitationTenant();
    $user = createInvitationUser($schoolId);
    $invitation = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL)->invitation;

    expect($invitation->consume())->toBeTrue()
        ->and($invitation->fresh()->isConsumed())->toBeTrue()
        ->and($invitation->fresh()->isUsable())->toBeFalse()
        ->and($invitation->consume())->toBeFalse();
});

it('cannot consume revoked or expired invitations', function (): void {
    $schoolId = createInvitationTenant();
    $user = createInvitationUser($schoolId);

    $revoked = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL)->invitation;
    $revoked->revoke();

    expect($revoked->fresh()->isRevoked())->toBeTrue()
        ->and($revoked->consume())->toBeFalse();

    $expiredUser = createInvitationUser($schoolId);
    $expired = UserInvitation::factory()->expired()->create([
        'user_id' => $expiredUser->id,
        'school_id' => $schoolId,
    ]);

    expect($expired->isExpired())->toBeTrue()
        ->and($expired->consume())->toBeFalse();
});

it('rejects invitation creation when user has no school', function (): void {
    $user = User::factory()->create(['school_id' => null, 'status' => 'pending']);

    expect(fn () => UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL))
        ->toThrow(InvalidArgumentException::class, 'school-scoped invitation');
});

it('rejects invitation creation when school is missing', function (): void {
    $schoolId = createInvitationTenant();
    $user = createInvitationUser($schoolId);

    expect(fn () => UserInvitation::query()->create([
        'user_id' => $user->id,
        'school_id' => '',
        'purpose' => UserInvitation::PURPOSE_STAFF_INITIAL,
        'token_hash' => hash('sha256', bin2hex(random_bytes(32))),
        'expires_at' => now()->addHours(48),
    ]))->toThrow(InvalidArgumentException::class, 'require a school_id');
});

it('rejects invitation creation when invitation school mismatches user school', function (): void {
    $schoolA = createInvitationTenant('School A');
    $schoolB = createInvitationTenant('School B');
    $user = createInvitationUser($schoolA);

    expect(fn () => UserInvitation::query()->create([
        'user_id' => $user->id,
        'school_id' => $schoolB,
        'purpose' => UserInvitation::PURPOSE_STAFF_INITIAL,
        'token_hash' => hash('sha256', bin2hex(random_bytes(32))),
        'expires_at' => now()->addHours(48),
    ]))->toThrow(InvalidArgumentException::class, 'must match');
});

it('revokes previous outstanding invitation when a replacement is issued', function (): void {
    $schoolId = createInvitationTenant();
    $user = createInvitationUser($schoolId);

    $first = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL)->invitation;
    $second = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL)->invitation;

    expect($first->fresh()->isRevoked())->toBeTrue()
        ->and($first->fresh()->isUsable())->toBeFalse()
        ->and($second->fresh()->isUsable())->toBeTrue()
        ->and($second->id)->not->toBe($first->id);

    expect(UserInvitation::query()->where('user_id', $user->id)->where('purpose', UserInvitation::PURPOSE_STAFF_INITIAL)->count())->toBe(2);
});

it('does not allow a consumed invitation to be reused by token lookup', function (): void {
    $schoolId = createInvitationTenant();
    $user = createInvitationUser($schoolId);

    $issue = UserInvitation::issue($user, UserInvitation::PURPOSE_STAFF_INITIAL);
    $plainToken = $issue->plainToken;

    expect(UserInvitation::findUsableByPlainToken($plainToken))->not->toBeNull();

    $issue->invitation->consume();

    expect(UserInvitation::findUsableByPlainToken($plainToken))->toBeNull();
});
