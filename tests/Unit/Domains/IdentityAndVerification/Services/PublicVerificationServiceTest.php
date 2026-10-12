<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\IdentityAndVerification\Application\Services\VerificationIdentityService;
use App\Domains\IdentityAndVerification\Domain\ValueObjects\PublicVerificationPolicy;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;

function publicVerificationService(): VerificationIdentityService
{
    return app(VerificationIdentityService::class);
}

function publicVerificationPayload(string $token): array
{
    $response = publicVerificationService()->publicVerification($token);

    return (new PublicVerificationPolicy())->filter($response->toArray());
}

function issuedTokenFor(AcademicDocument $document): string
{
    return publicVerificationService()->issueForDocument($document->school_id, $document->getKey());
}

test('an unknown well-formed token is indistinguishable from an unpublished document', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    issuedTokenFor($document);

    expect(publicVerificationPayload(VerificationIdentityService::generateToken()))
        ->toBe(['verdict' => 'no_public_record']);
});

test('malformed tokens always hash first and answer the uniform response', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    issuedTokenFor($document);

    expect(publicVerificationPayload('this.has.a.dot'))->toBe(['verdict' => 'no_public_record']);
    expect(publicVerificationPayload(str_repeat('A', 200)))->toBe(['verdict' => 'no_public_record']);
});

test('unpublished lifecycle states never become public records, even when an identity exists', function (): void {
    foreach (['draft', 'generated', 'reviewed', 'approved'] as $status) {
        $school = makeTenant();
        $document = AcademicDocument::factory()->forSchool($school)->create();

        if ($status !== 'draft') {
            $document->update(['status' => DocumentStatus::from($status)]);
        }

        $payload = publicVerificationPayload(issuedTokenFor($document));

        expect($payload, $status)->toBe(['verdict' => 'no_public_record']);
    }
});

test('a published document answers the authentic-published LEVEL-2 payload', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();

    $payload = publicVerificationPayload(issuedTokenFor($document));

    expect($payload)->toBe([
        'verdict' => 'authentic_published',
        'document_number' => $document->document_number,
        'document_type' => $document->document_type->value,
        'status' => 'published',
        'issued_at' => $document->generated_at->toIso8601String(),
    ]);
});

test('an archived document stays historically verifiable and distinct from published', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->archived()->create();

    $payload = publicVerificationPayload(issuedTokenFor($document));

    expect($payload)->toHaveKeys(['verdict', 'document_number', 'document_type', 'status', 'issued_at'])
        ->and($payload['verdict'])->toBe('authentic_archived')
        ->and($payload['status'])->toBe('archived');
});

test('a document without an issue date omits issued_at instead of guessing', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    $document->update(['generated_at' => null]);

    $payload = publicVerificationPayload(issuedTokenFor($document));

    expect($payload)->toBe([
        'verdict' => 'authentic_published',
        'document_number' => $document->document_number,
        'document_type' => $document->document_type->value,
        'status' => 'published',
    ]);
});

test('an officially revoked identity answers authentic-officially-revoked, never archived', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    $token = VerificationIdentityService::generateToken();

    VerificationIdentity::factory()
        ->forDocument($document)
        ->revoked()
        ->create(['token_hash' => VerificationIdentityService::hashToken($token)]);

    $payload = publicVerificationPayload($token);

    expect($payload)->toBe([
        'verdict' => 'authentic_officially_revoked',
        'document_number' => $document->document_number,
        'document_type' => $document->document_type->value,
        'status' => 'revoked',
    ]);
});

test('a replaced identity answers authentic-superseded', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->archived()->create();
    $token = VerificationIdentityService::generateToken();

    VerificationIdentity::factory()
        ->forDocument($document)
        ->replaced()
        ->create(['token_hash' => VerificationIdentityService::hashToken($token)]);

    $payload = publicVerificationPayload($token);

    expect($payload)->toBe([
        'verdict' => 'authentic_superseded',
        'document_number' => $document->document_number,
        'document_type' => $document->document_type->value,
        'status' => 'replaced',
    ]);
});

test('an identity whose document vanished answers the uniform response', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    $token = issuedTokenFor($document);

    $document->forceDelete();

    expect(publicVerificationPayload($token))->toBe(['verdict' => 'no_public_record']);
});

test('a school A identity never answers with school B document data', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $documentA = AcademicDocument::factory()->forSchool($schoolA)->published()->create();
    $tokenA = issuedTokenFor($documentA);

    AcademicDocument::factory()->forSchool($schoolB)->published()->create();

    $payload = publicVerificationPayload($tokenA);

    expect($payload['document_number'])->toBe($documentA->document_number);
});
