<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Application\AcademicCommunicationService;
use App\Domains\AcademicCommunication\Application\DTOs\ArchiveDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\RegenerateDocumentData;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\IdentityAndVerification\Application\Services\VerificationIdentityService;
use App\Domains\IdentityAndVerification\Domain\Enums\VerificationStatus;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Tests\Support\FakeRenderer;

/**
 * Canonical verification URL stability across the document life (SPR-016
 * Phase 5, ADR-022 Decision 5 / I8).
 *
 * The canonical URL is a deterministic function of the immutable opaque token,
 * so it never rotates with document regeneration, version creation, archival,
 * revocation or replacement — the agent (school/rendering substrate) keeps
 * resolving the same URL while only the public verdict changes. The URL always
 * targets the existing Phase 3 public route.
 */
beforeEach(function (): void {
    $this->app->instance(DocumentRendererContract::class, new FakeRenderer());
    $this->identities = app(VerificationIdentityService::class);
    $this->communication = app(AcademicCommunicationService::class);
});

function grantVerificationUrlRevoke(User $user): void
{
    $user->givePermissionTo(Permission::findOrCreate('document.revoke', 'web'));
}

test('regeneration and version creation keep the identity, token and canonical URL stable', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->create(['academic_document_template_id' => AcademicDocumentTemplate::factory()->forSchool($school)->create()->getKey()]);

    $token = $this->identities->issueForDocument($school, $document->getKey());
    $urlBefore = $this->identities->verificationUrlForToken($token)->toString();
    $hashBefore = VerificationIdentity::forSchool($school)
        ->where('academic_document_id', $document->getKey())
        ->first()->token_hash;

    $version = $this->communication->regenerate(RegenerateDocumentData::fromArray([
        'school_id' => $school,
        'document_id' => $document->id,
        'content' => ['student' => ['full_name' => 'Corrected holder']],
    ]));

    expect($document->refresh()->versions()->count())->toBe(1)
        ->and($version->version_number)->toBe(1)
        ->and(VerificationIdentity::forSchool($school)->where('academic_document_id', $document->getKey())->count())->toBe(1)
        ->and(VerificationIdentity::forSchool($school)->where('academic_document_id', $document->getKey())->first()->token_hash)->toBe($hashBefore)
        ->and($this->identities->verificationUrlForToken($token)->toString())->toBe($urlBefore)
        ->and($this->identities->resolve($token)->documentId)->toBe($document->getKey());
});

test('archival keeps the canonical URL stable and only the verdict changes', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();

    $token = $this->identities->issueForDocument($school, $document->getKey());
    $urlBefore = $this->identities->verificationUrlForToken($token)->toString();
    $hashBefore = VerificationIdentity::forSchool($school)->where('academic_document_id', $document->getKey())->first()->token_hash;

    $this->communication->archive(ArchiveDocumentData::fromArray([
        'school_id' => $school,
        'document_id' => $document->id,
    ]));

    expect($document->refresh()->status)->toBe(DocumentStatus::Archived)
        ->and(VerificationIdentity::forSchool($school)->where('academic_document_id', $document->getKey())->first()->token_hash)->toBe($hashBefore)
        ->and($this->identities->verificationUrlForToken($token)->toString())->toBe($urlBefore);

    $this->getJson($this->identities->verificationUrlForToken($token)->toString())
        ->assertOk()
        ->assertJsonPath('verdict', 'authentic_archived');
});

test('revocation keeps the canonical URL stable and only the verdict changes', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();

    $token = $this->identities->issueForDocument($school, $document->getKey());
    $urlBefore = $this->identities->verificationUrlForToken($token)->toString();
    $hashBefore = VerificationIdentity::forSchool($school)->where('academic_document_id', $document->getKey())->first()->token_hash;

    $actor = User::factory()->create(['school_id' => $school]);
    grantVerificationUrlRevoke($actor);

    $this->identities->revoke($school, $document->getKey(), 'Withdrawn after a correction request.', $actor);

    $identity = VerificationIdentity::forSchool($school)->where('academic_document_id', $document->getKey())->first();

    expect($identity->status)->toBe(VerificationStatus::Revoked)
        ->and($identity->token_hash)->toBe($hashBefore)
        ->and($this->identities->verificationUrlForToken($token)->toString())->toBe($urlBefore);

    $this->getJson($this->identities->verificationUrlForToken($token)->toString())
        ->assertOk()
        ->assertJsonPath('verdict', 'authentic_officially_revoked');
});

test('replacement keeps the superseded canonical URL stable and only the verdict changes', function (): void {
    $school = makeTenant();
    $superseded = AcademicDocument::factory()->forSchool($school)->archived()->create();
    $successor = AcademicDocument::factory()->forSchool($school)->published()->create();

    $oldToken = $this->identities->issueForDocument($school, $superseded->getKey());
    $newToken = $this->identities->issueForDocument($school, $successor->getKey());
    $oldUrlBefore = $this->identities->verificationUrlForToken($oldToken)->toString();
    $oldHash = VerificationIdentity::forSchool($school)->where('academic_document_id', $superseded->getKey())->first()->token_hash;

    $actor = User::factory()->create(['school_id' => $school]);
    grantVerificationUrlRevoke($actor);

    $this->identities->replaceWith($school, $superseded->getKey(), $successor->getKey(), 'Superseded by an updated issue.', $actor);

    $identity = VerificationIdentity::forSchool($school)->where('academic_document_id', $superseded->getKey())->first();

    expect($identity->status)->toBe(VerificationStatus::Replaced)
        ->and($identity->token_hash)->toBe($oldHash)
        ->and($identity->replaced_by_document_number)->toBe($successor->document_number)
        ->and($this->identities->verificationUrlForToken($oldToken)->toString())->toBe($oldUrlBefore)
        ->and($this->identities->verificationUrlForToken($oldToken)->toString())
            ->not->toBe($this->identities->verificationUrlForToken($newToken)->toString());

    $this->getJson($this->identities->verificationUrlForToken($oldToken)->toString())
        ->assertOk()
        ->assertJsonPath('verdict', 'authentic_superseded');
});

test('a new document receives its own identity, token and canonical URL', function (): void {
    $school = makeTenant();
    $documentA = AcademicDocument::factory()->forSchool($school)->published()->create();
    $documentB = AcademicDocument::factory()->forSchool($school)->published()->create();

    $tokenA = $this->identities->issueForDocument($school, $documentA->getKey());
    $tokenB = $this->identities->issueForDocument($school, $documentB->getKey());

    $urlA = $this->identities->verificationUrlForToken($tokenA)->toString();
    $urlB = $this->identities->verificationUrlForToken($tokenB)->toString();

    expect(VerificationIdentity::forSchool($school)->count())->toBe(2)
        ->and($tokenA)->not->toBe($tokenB)
        ->and($urlA)->not->toBe($urlB);
});

test('the canonical URL targets the existing Phase 3 public verification route', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();

    $token = $this->identities->issueForDocument($school, $document->getKey());
    $url = $this->identities->verificationUrlForToken($token);

    $route = Route::getRoutes()->getByName('verification.public');

    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('v/{token}');

    $expected = rtrim((string) config('app.url'), '/').route('verification.public', ['token' => $token], absolute: false);

    expect($url->toString())->toBe($expected);

    $this->getJson($url->toString())
        ->assertOk()
        ->assertJsonPath('verdict', 'authentic_published');
});
