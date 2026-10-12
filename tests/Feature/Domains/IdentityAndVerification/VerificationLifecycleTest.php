<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\IdentityAndVerification\Application\Services\VerificationIdentityService;
use App\Domains\IdentityAndVerification\Domain\Enums\VerificationStatus;
use App\Domains\IdentityAndVerification\Domain\Exceptions\InvalidVerificationTransitionException;
use App\Domains\IdentityAndVerification\Domain\Exceptions\VerificationDocumentNotFoundException;
use App\Domains\IdentityAndVerification\Domain\Exceptions\VerificationDocumentNotPubliclyVerifiableException;
use App\Domains\IdentityAndVerification\Domain\Exceptions\VerificationIdentityNotFoundException;
use App\Domains\IdentityAndVerification\Domain\Exceptions\VerificationReasonRequiredException;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function grantDocumentRevoke(User $user): void
{
    $user->givePermissionTo(Permission::findOrCreate('document.revoke', 'web'));
}

test('a principal with document.revoke revokes an active identity and it stays historically resolvable', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    $token = app(VerificationIdentityService::class)->issueForDocument($school, $document->getKey());
    $actor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($actor);

    $identity = app(VerificationIdentityService::class)->revoke($school, $document->getKey(), 'Confidential correction requested.', $actor);

    expect($identity->status)->toBe(VerificationStatus::Revoked)
        ->and($identity->revoked_at)->not->toBeNull()
        ->and($identity->revocation_reason)->toBe('Confidential correction requested.')
        ->and($identity->token_hash)->not->toBeNull();

    $response = $this->getJson(route('verification.public', ['token' => $token]));

    $response->assertOk()
        ->assertExactJson([
            'verdict' => 'authentic_officially_revoked',
            'document_number' => $document->document_number,
            'document_type' => $document->document_type->value,
            'status' => 'revoked',
        ]);

    expect($response->getContent())->not->toContain('Confidential')
        ->and($response->getContent())->not->toContain('revocation');
});

test('an archived document identity can still be revoked', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->archived()->create();
    $token = app(VerificationIdentityService::class)->issueForDocument($school, $document->getKey());
    $actor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($actor);

    $identity = app(VerificationIdentityService::class)->revoke($school, $document->getKey(), 'Historical correction.', $actor);

    expect($identity->status)->toBe(VerificationStatus::Revoked);

    $this->getJson(route('verification.public', ['token' => $token]))
        ->assertOk()
        ->assertJsonPath('verdict', 'authentic_officially_revoked');
});

test('revocation requires a meaningful reason', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    app(VerificationIdentityService::class)->issueForDocument($school, $document->getKey());
    $actor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($actor);

    foreach (['', '   '] as $reason) {
        expect(fn () => app(VerificationIdentityService::class)->revoke($school, $document->getKey(), $reason, $actor))
            ->toThrow(VerificationReasonRequiredException::class);
    }
});

test('only publicly verifiable documents can be revoked', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->create();
    app(VerificationIdentityService::class)->issueForDocument($school, $document->getKey());
    $actor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($actor);

    expect(fn () => app(VerificationIdentityService::class)->revoke($school, $document->getKey(), 'Reason', $actor))
        ->toThrow(VerificationDocumentNotPubliclyVerifiableException::class);
});

test('revoking an identity that does not exist is refused', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    $actor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($actor);

    expect(fn () => app(VerificationIdentityService::class)->revoke($school, $document->getKey(), 'Reason', $actor))
        ->toThrow(VerificationIdentityNotFoundException::class);
});

test('a revoked identity is terminal: it cannot be revoked or replaced again', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    app(VerificationIdentityService::class)->issueForDocument($school, $document->getKey());
    $successor = AcademicDocument::factory()->forSchool($school)->published()->create();
    $actor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($actor);

    app(VerificationIdentityService::class)->revoke($school, $document->getKey(), 'First reason.', $actor);

    expect(fn () => app(VerificationIdentityService::class)->revoke($school, $document->getKey(), 'Again.', $actor))
        ->toThrow(InvalidVerificationTransitionException::class)
        ->and(fn () => app(VerificationIdentityService::class)->replaceWith($school, $document->getKey(), $successor->getKey(), 'Replace.', $actor))
        ->toThrow(InvalidVerificationTransitionException::class);
});

test('a principal with document.revoke replaces an identity and links the successor', function (): void {
    $school = makeTenant();
    $superseded = AcademicDocument::factory()->forSchool($school)->published()->create();
    $successor = AcademicDocument::factory()->forSchool($school)->published()->create();
    $token = app(VerificationIdentityService::class)->issueForDocument($school, $superseded->getKey());
    $allActor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($allActor);

    $identity = app(VerificationIdentityService::class)->replaceWith(
        $school,
        $superseded->getKey(),
        $successor->getKey(),
        'Corrected transcript re-issued.',
        $allActor,
    );

    expect($identity->status)->toBe(VerificationStatus::Replaced)
        ->and($identity->replaced_at)->not->toBeNull()
        ->and($identity->replaced_by_document_number)->toBe($successor->document_number)
        ->and($identity->replaced_by_document_number)->not->toBe($superseded->document_number);

    $this->getJson(route('verification.public', ['token' => $token]))
        ->assertOk()
        ->assertExactJson([
            'verdict' => 'authentic_superseded',
            'document_number' => $superseded->document_number,
            'document_type' => $superseded->document_type->value,
            'status' => 'replaced',
        ]);

    $successorToken = app(VerificationIdentityService::class)->issueForDocument($school, $successor->getKey());

    expect($successorToken)->not->toBe($token);

    $this->getJson(route('verification.public', ['token' => $successorToken]))
        ->assertOk()
        ->assertJsonPath('verdict', 'authentic_published');
});

test('replacement requires a reason', function (): void {
    $school = makeTenant();
    $superseded = AcademicDocument::factory()->forSchool($school)->published()->create();
    $successor = AcademicDocument::factory()->forSchool($school)->published()->create();
    app(VerificationIdentityService::class)->issueForDocument($school, $superseded->getKey());
    $actor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($actor);

    expect(fn () => app(VerificationIdentityService::class)->replaceWith($school, $superseded->getKey(), $successor->getKey(), '', $actor))
        ->toThrow(VerificationReasonRequiredException::class);
});

test('replacement fails when the successor belongs to another school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();
    $superseded = AcademicDocument::factory()->forSchool($schoolA)->published()->create();
    $foreignSuccessor = AcademicDocument::factory()->forSchool($schoolB)->published()->create();
    app(VerificationIdentityService::class)->issueForDocument($schoolA, $superseded->getKey());
    $actor = User::factory()->create(['school_id' => $schoolA]);
    grantDocumentRevoke($actor);

    expect(fn () => app(VerificationIdentityService::class)->replaceWith($schoolA, $superseded->getKey(), $foreignSuccessor->getKey(), 'Reason.', $actor))
        ->toThrow(VerificationDocumentNotFoundException::class);
});

test('replacement fails when the successor is the superseded document itself', function (): void {
    $school = makeTenant();
    $superseded = AcademicDocument::factory()->forSchool($school)->published()->create();
    app(VerificationIdentityService::class)->issueForDocument($school, $superseded->getKey());
    $actor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($actor);

    expect(fn () => app(VerificationIdentityService::class)->replaceWith($school, $superseded->getKey(), $superseded->getKey(), 'Reason.', $actor))
        ->toThrow(InvalidVerificationTransitionException::class);
});

test('replacement requires a publicly verifiable superseded document', function (): void {
    $school = makeTenant();
    $superseded = AcademicDocument::factory()->forSchool($school)->approved()->create();
    $successor = AcademicDocument::factory()->forSchool($school)->published()->create();
    app(VerificationIdentityService::class)->issueForDocument($school, $superseded->getKey());
    $actor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($actor);

    expect(fn () => app(VerificationIdentityService::class)->replaceWith($school, $superseded->getKey(), $successor->getKey(), 'Reason.', $actor))
        ->toThrow(VerificationDocumentNotPubliclyVerifiableException::class);
});

test('replaced identities are terminal and cannot be invalidated further', function (): void {
    $school = makeTenant();
    $superseded = AcademicDocument::factory()->forSchool($school)->published()->create();
    $successor = AcademicDocument::factory()->forSchool($school)->published()->create();
    $third = AcademicDocument::factory()->forSchool($school)->published()->create();
    app(VerificationIdentityService::class)->issueForDocument($school, $superseded->getKey());
    $actor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($actor);

    app(VerificationIdentityService::class)->replaceWith($school, $superseded->getKey(), $successor->getKey(), 'Reason.', $actor);

    expect(fn () => app(VerificationIdentityService::class)->revoke($school, $superseded->getKey(), 'Revoke.', $actor))
        ->toThrow(InvalidVerificationTransitionException::class)
        ->and(fn () => app(VerificationIdentityService::class)->replaceWith($school, $superseded->getKey(), $third->getKey(), 'Replace.', $actor))
        ->toThrow(InvalidVerificationTransitionException::class);
});

test('an actor without document.revoke cannot revoke or replace', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    $successor = AcademicDocument::factory()->forSchool($school)->published()->create();
    app(VerificationIdentityService::class)->issueForDocument($school, $document->getKey());

    Permission::findOrCreate('document.revoke', 'web');

    $staff = User::factory()->create(['school_id' => $school]);

    expect(fn () => app(VerificationIdentityService::class)->revoke($school, $document->getKey(), 'Reason.', $staff))
        ->toThrow(AuthorizationException::class)
        ->and(fn () => app(VerificationIdentityService::class)->replaceWith($school, $document->getKey(), $successor->getKey(), 'Reason.', $staff))
        ->toThrow(AuthorizationException::class);
});

test('an actor of another school cannot invalidate the identity even with the permission', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();
    $document = AcademicDocument::factory()->forSchool($schoolA)->published()->create();
    app(VerificationIdentityService::class)->issueForDocument($schoolA, $document->getKey());
    $foreignActor = User::factory()->create(['school_id' => $schoolB]);
    grantDocumentRevoke($foreignActor);

    expect(fn () => app(VerificationIdentityService::class)->revoke($schoolA, $document->getKey(), 'Reason.', $foreignActor))
        ->toThrow(AuthorizationException::class);
});

test('a late invalidation without an actor is refused', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    app(VerificationIdentityService::class)->issueForDocument($school, $document->getKey());

    expect(fn () => app(VerificationIdentityService::class)->revoke($school, $document->getKey(), 'Reason.'))
        ->toThrow(AuthorizationException::class);
});

test('the super admin bypass authorizes the lifecycle without the capability', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    app(VerificationIdentityService::class)->issueForDocument($school, $document->getKey());
    $superAdmin = User::factory()->create(['school_id' => makeTenant('Platform School')]);
    $superAdmin->assignRole(Role::findOrCreate('super_admin', 'web'));

    $identity = app(VerificationIdentityService::class)->revoke($school, $document->getKey(), 'Supervisory override.', $superAdmin);

    expect($identity->status)->toBe(VerificationStatus::Revoked);
});

test('audit log records revocation with actor, school and reason', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    $identity = app(VerificationIdentityService::class)->issueForDocument($school, $document->getKey());
    $actor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($actor);

    $identity = VerificationIdentity::query()
        ->forSchool($school)
        ->where('academic_document_id', $document->getKey())
        ->firstOrFail();

    app(VerificationIdentityService::class)->revoke($school, $document->getKey(), 'Complaint received.', $actor);

    expect(AuditLog::query()->forSchool($school)->get())->toHaveCount(1);

    $log = AuditLog::query()->forSchool($school)->firstOrFail();

    expect($log->action)->toBe('verification_identity_revoked')
        ->and($log->auditable_type)->toBe(VerificationIdentity::class)
        ->and((string) $log->auditable_id)->toBe((string) $identity->getKey())
        ->and((string) $log->user_id)->toBe((string) $actor->getKey())
        ->and($log->user_type)->toBe(User::class)
        ->and($log->new_values['reason'])->toBe('Complaint received.');
});

test('audit log records replacement with the successor reference and reason', function (): void {
    $school = makeTenant();
    $superseded = AcademicDocument::factory()->forSchool($school)->published()->create();
    $successor = AcademicDocument::factory()->forSchool($school)->published()->create();
    app(VerificationIdentityService::class)->issueForDocument($school, $superseded->getKey());
    $actor = User::factory()->create(['school_id' => $school]);
    grantDocumentRevoke($actor);

    app(VerificationIdentityService::class)->replaceWith($school, $superseded->getKey(), $successor->getKey(), 'Re-issued.', $actor);

    $log = AuditLog::query()->forSchool($school)->firstOrFail();

    expect($log->action)->toBe('verification_identity_replaced')
        ->and($log->new_values['replaced_by_document_number'])->toBe($successor->document_number)
        ->and($log->new_values['reason'])->toBe('Re-issued.');
});

test('no privileged revocation or replacement endpoint exists on the public surface', function (): void {
    expect(Route::getRoutes()->getByName('verification.revoke'))->toBeNull()
        ->and(Route::getRoutes()->getByName('verification.replace'))->toBeNull();

    $public = Route::getRoutes()->getByName('verification.public');

    expect($public)->not->toBeNull()
        ->and($public->methods())->toContain('GET');
});
