<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\IdentityAndVerification\Application\Services\VerificationIdentityService;
use App\Domains\IdentityAndVerification\Domain\Enums\VerificationEligibilityVerdict;
use App\Domains\IdentityAndVerification\Domain\Exceptions\VerificationDocumentNotFoundException;
use App\Domains\IdentityAndVerification\Domain\Exceptions\VerificationIdentityAlreadyExistsException;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function verificationIdentityService(): VerificationIdentityService
{
    return app(VerificationIdentityService::class);
}

function makeVerificationDocument(string $schoolId): AcademicDocument
{
    return AcademicDocument::factory()
        ->forSchool($schoolId)
        ->create();
}

test('generateToken returns a typeable base64url token with at least 144 bits of entropy', function (): void {
    $token = VerificationIdentityService::generateToken();

    expect($token)->toBeString()->not->toBeEmpty();
    expect(strlen($token))->toBe(32);
    expect(preg_match('/^[A-Za-z0-9_-]+$/', $token))->toBe(1);
    expect($token)->not->toContain('=');

    $bytes = base64_decode(strtr($token, '-_', '+/'), true);
    expect($bytes)->not->toBeFalse();
    expect(strlen((string) $bytes))->toBe(24);
    expect(strlen((string) $bytes) * 8)->toBeGreaterThanOrEqual(144);
});

test('generated tokens are unique and independent from document identity values', function (): void {
    $school = makeTenant();
    $document = makeVerificationDocument($school);

    $tokenA = VerificationIdentityService::generateToken();
    $tokenB = VerificationIdentityService::generateToken();

    expect($tokenA)->not->toBe($tokenB);
    expect($tokenA)->not->toBe((string) $document->getKey());
    expect($tokenA)->not->toBe((string) $document->document_number);
    expect((string) $document->getKey())->not->toContain($tokenA);
    expect((string) $document->document_number)->not->toContain($tokenA);
});

test('only the complete SHA-256 digest of the token is persisted', function (): void {
    $school = makeTenant();
    $document = makeVerificationDocument($school);

    $token = verificationIdentityService()->issueForDocument($school, $document->getKey());

    $identity = VerificationIdentity::forSchool($school)
        ->where('academic_document_id', $document->getKey())
        ->first();

    expect($identity)->not->toBeNull();
    expect($identity->token_hash)->toHaveLength(64);
    expect($identity->token_hash)->toBe(VerificationIdentityService::hashToken($token));
    expect($identity->token_hash)->not->toBe($token);
    expect((bool) preg_match('/^[a-f0-9]{64}$/', $identity->token_hash))->toBeTrue();

    $row = DB::table('verification_identities')->where('id', $identity->getKey())->first();
    expect(array_values(get_object_vars($row)))->not->toContain($token);
});

test('a token resolves through its digest and unknown tokens resolve to null', function (): void {
    $school = makeTenant();
    $document = makeVerificationDocument($school);

    $token = verificationIdentityService()->issueForDocument($school, $document->getKey());

    $detail = verificationIdentityService()->resolve($token);

    expect($detail)->not->toBeNull();
    expect($detail->documentId)->toBe($document->getKey());
    expect($detail->schoolId)->toBe($school);
    expect($detail->documentStatus)->toBe('draft');
    expect($detail->publiclyVerifiable)->toBeFalse();

    expect(verificationIdentityService()->resolve(str_repeat('x', 32)))->toBeNull();
});

test('one identity per document: a second issuance for the same document is refused', function (): void {
    $school = makeTenant();
    $document = makeVerificationDocument($school);

    verificationIdentityService()->issueForDocument($school, $document->getKey());
    verificationIdentityService()->issueForDocument($school, $document->getKey());
})->throws(VerificationIdentityAlreadyExistsException::class);

test('aggregate changes never create another identity; a new document receives its own', function (): void {
    $school = makeTenant();
    $document = makeVerificationDocument($school);

    verificationIdentityService()->issueForDocument($school, $document->getKey());

    $document->update(['content_version' => 2]);

    expect(VerificationIdentity::forSchool($school)->count())->toBe(1);

    $other = makeVerificationDocument($school);
    $token = verificationIdentityService()->issueForDocument($school, $other->getKey());

    expect(VerificationIdentity::forSchool($school)->count())->toBe(2);
    expect(verificationIdentityService()->resolve($token)->documentId)->toBe($other->getKey());
});

test('issuance refuses a document owned by another school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();
    $documentB = makeVerificationDocument($schoolB);

    verificationIdentityService()->issueForDocument($schoolA, $documentB->getKey());
})->throws(VerificationDocumentNotFoundException::class);

test('an identity of school A never resolves school B records', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $documentA = makeVerificationDocument($schoolA);
    $token = verificationIdentityService()->issueForDocument($schoolA, $documentA->getKey());

    $detail = verificationIdentityService()->resolve($token);

    expect($detail)->not->toBeNull();
    expect($detail->schoolId)->toBe($schoolA);
    expect($detail->documentId)->toBe($documentA->getKey());
    expect(VerificationIdentity::forSchool($schoolB)->count())->toBe(0);
});

test('the identity record stores only routing metadata, never PII or content', function (): void {
    $school = makeTenant();
    $document = makeVerificationDocument($school);

    verificationIdentityService()->issueForDocument($school, $document->getKey());

    $columns = Schema::getColumnListing('verification_identities');

    expect($columns)->toContain('school_id')
        ->and($columns)->toContain('academic_document_id')
        ->and($columns)->toContain('token_hash')
        ->and($columns)->toContain('status');

    foreach (['student_id', 'student_name', 'content', 'metadata', 'email', 'phone', 'address'] as $forbidden) {
        expect($columns)->not->toContain($forbidden);
    }
});

test('a published document resolves as publicly verifiable (authentic-published)', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();

    $token = verificationIdentityService()->issueForDocument($school, $document->getKey());
    $detail = verificationIdentityService()->resolve($token);

    expect($detail)->not->toBeNull();
    expect($detail->documentStatus)->toBe('published');
    expect($detail->verdict)->toBe(VerificationEligibilityVerdict::AuthenticPublished);
    expect($detail->publiclyVerifiable)->toBeTrue();
});

test('an archived document resolves as historically verifiable (authentic-archived)', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->archived()->create();

    $token = verificationIdentityService()->issueForDocument($school, $document->getKey());
    $detail = verificationIdentityService()->resolve($token);

    expect($detail)->not->toBeNull();
    expect($detail->documentStatus)->toBe('archived');
    expect($detail->verdict)->toBe(VerificationEligibilityVerdict::AuthenticArchived);
    expect($detail->publiclyVerifiable)->toBeTrue();
});
