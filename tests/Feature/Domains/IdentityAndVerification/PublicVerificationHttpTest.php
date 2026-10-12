<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\IdentityAndVerification\Application\Services\VerificationIdentityService;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

function publicVerificationUrl(string $token): string
{
    return route('verification.public', ['token' => $token]);
}

function publicVerificationTokenFor(AcademicDocument $document): string
{
    return app(VerificationIdentityService::class)->issueForDocument(
        (string) $document->school_id,
        $document->getKey(),
    );
}

function issuedTokenWithStatus(AcademicDocument $document, string $status): string
{
    $token = VerificationIdentityService::generateToken();

    VerificationIdentity::factory()
        ->forDocument($document)
        ->{$status}()
        ->create(['token_hash' => VerificationIdentityService::hashToken($token)]);

    return $token;
}

beforeEach(function (): void {
    Cache::flush();
});

test('the public verification route exists as an unauthenticated GET /v/{token} endpoint', function (): void {
    $route = Route::getRoutes()->getByName('verification.public');

    expect($route)->not->toBeNull();
    expect($route->methods())->toContain('GET');
    expect($route->uri())->toBe('v/{token}');

    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();

    $this->getJson(publicVerificationUrl(publicVerificationTokenFor($document)))
        ->assertOk()
        ->assertJsonPath('verdict', 'authentic_published');
});

test('the endpoint resolves a valid token for a published document to the full LEVEL-2 payload', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    $token = publicVerificationTokenFor($document);

    $expected = [
        'verdict' => 'authentic_published',
        'document_number' => $document->document_number,
        'document_type' => $document->document_type->value,
        'status' => 'published',
        'issued_at' => $document->refresh()->generated_at->toIso8601String(),
    ];

    $response = $this->getJson(publicVerificationUrl($token));

    $response->assertOk()
        ->assertExactJson($expected);

    expect($response->getContent())->not->toContain($token);
});

test('archived documents remain historically verifiable and distinct', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->archived()->create();

    $this->getJson(publicVerificationUrl(publicVerificationTokenFor($document)))
        ->assertOk()
        ->assertJsonPath('verdict', 'authentic_archived')
        ->assertJsonPath('status', 'archived');
});

test('unpublished lifecycle states answer the uniform non-revealing response', function (): void {
    foreach (['draft', 'generated', 'reviewed', 'approved'] as $status) {
        $school = makeTenant();
        $document = AcademicDocument::factory()->forSchool($school)->create();

        if ($status !== 'draft') {
            $document->update(['status' => DocumentStatus::from($status)]);
        }

        $this->getJson(publicVerificationUrl(publicVerificationTokenFor($document)))
            ->assertOk()
            ->assertExactJson(['verdict' => 'no_public_record']);
    }
});

test('an officially revoked identity is never collapsed into the archived verdict', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    $token = issuedTokenWithStatus($document, 'revoked');

    $this->getJson(publicVerificationUrl($token))
        ->assertOk()
        ->assertExactJson([
            'verdict' => 'authentic_officially_revoked',
            'document_number' => $document->document_number,
            'document_type' => $document->document_type->value,
            'status' => 'revoked',
        ]);
});

test('a replaced identity answers authentic-superseded', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->archived()->create();
    $token = issuedTokenWithStatus($document, 'replaced');

    $this->getJson(publicVerificationUrl($token))
        ->assertOk()
        ->assertExactJson([
            'verdict' => 'authentic_superseded',
            'document_number' => $document->document_number,
            'document_type' => $document->document_type->value,
            'status' => 'replaced',
        ]);
});

test('unknown and malformed tokens answer the exact same body as unpublished documents', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    publicVerificationTokenFor($document);

    $unknown = VerificationIdentityService::generateToken();
    $malformedChars = 'this.has.a.dot';
    $tooLong = str_repeat('A', 200);

    foreach ([$unknown, $malformedChars, $tooLong] as $token) {
        $this->getJson(publicVerificationUrl($token))
            ->assertOk()
            ->assertExactJson(['verdict' => 'no_public_record']);
    }
});

test('the response exposes exactly the LEVEL-2 whitelist and never the token or tenant data', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    $token = publicVerificationTokenFor($document);

    $response = $this->getJson(publicVerificationUrl($token));

    $response->assertOk();

    expect(array_keys($response->json()))->toBe([
        'verdict',
        'document_number',
        'document_type',
        'status',
        'issued_at',
    ]);

    expect($response->json())->not->toHaveKey('id')
        ->and($response->json())->not->toHaveKey('school_id');

    $body = $response->getContent();

    expect($body)->not->toContain($token)
        ->and($body)->not->toContain($school)
        ->and($body)->not->toContain('student')
        ->and($body)->not->toContain('title')
        ->and($body)->not->toContain('content')
        ->and($body)->not->toContain('email')
        ->and($body)->not->toContain('token');
});

test('tenant isolation holds: a client-supplied school_id never redirects the lookup', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $documentA = AcademicDocument::factory()->forSchool($schoolA)->published()->create();
    $tokenA = publicVerificationTokenFor($documentA);

    AcademicDocument::factory()->forSchool($schoolB)->published()->create();

    $expected = [
        'verdict' => 'authentic_published',
        'document_number' => $documentA->document_number,
        'document_type' => $documentA->document_type->value,
        'status' => 'published',
        'issued_at' => $documentA->refresh()->generated_at->toIso8601String(),
    ];

    $this->getJson(publicVerificationUrl($tokenA))
        ->assertOk()
        ->assertExactJson($expected);

    $manipulated = $this->getJson(publicVerificationUrl($tokenA).'?school_id='.$schoolB.'&academic_document_id=99999');

    $manipulated->assertOk()
        ->assertExactJson($expected);

    expect($manipulated->getContent())->not->toContain($schoolB);
});

test('the endpoint resists enumeration with identical bodies for every unknown token', function (): void {
    $school = makeTenant();
    AcademicDocument::factory()->forSchool($school)->published()->create();

    for ($i = 0; $i < 5; $i++) {
        $this->getJson(publicVerificationUrl(VerificationIdentityService::generateToken()))
            ->assertOk()
            ->assertExactJson(['verdict' => 'no_public_record']);
    }
});

test('the endpoint rate-limits per requester IP', function (): void {
    $school = makeTenant();
    AcademicDocument::factory()->forSchool($school)->published()->create();

    for ($i = 0; $i < 60; $i++) {
        $this->getJson(publicVerificationUrl(VerificationIdentityService::generateToken()))
            ->assertOk();
    }

    $this->getJson(publicVerificationUrl(VerificationIdentityService::generateToken()))
        ->assertStatus(429);
});

test('the endpoint rate-limits per individual token digest', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->published()->create();
    $token = publicVerificationTokenFor($document);

    for ($i = 0; $i < 10; $i++) {
        $this->getJson(publicVerificationUrl($token))
            ->assertOk();
    }

    $this->getJson(publicVerificationUrl($token))
        ->assertStatus(429);
});
