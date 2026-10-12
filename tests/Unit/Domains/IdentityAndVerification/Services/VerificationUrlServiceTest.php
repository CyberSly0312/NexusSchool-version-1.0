<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\IdentityAndVerification\Application\Services\VerificationIdentityService;
use App\Domains\IdentityAndVerification\Domain\ValueObjects\VerificationUrl;

/**
 * Canonical verification URL wiring (SPR-016 Phase 5, ADR-022 Decision 5).
 *
 * The application service composes the URL from the existing canonical
 * application URL (config('app.url')) and the relative path of the existing
 * named public verification route, so the canonical URL targets the Phase 3
 * route and never inherits a tenant domain.
 */
test('verificationUrlForToken composes the canonical URL from APP_URL and the public route', function (): void {
    $token = VerificationIdentityService::generateToken();

    $url = app(VerificationIdentityService::class)->verificationUrlForToken($token);

    $expected = rtrim((string) config('app.url'), '/').route('verification.public', ['token' => $token], absolute: false);

    expect($url)->toBeInstanceOf(VerificationUrl::class)
        ->and($url->toString())->toBe($expected)
        ->and($url->token)->toBe($token);
});

test('the canonical URL is absolute, uses /v/{token} and resolves to the public route', function (): void {
    $token = VerificationIdentityService::generateToken();

    $url = app(VerificationIdentityService::class)->verificationUrlForToken($token);

    expect($url->url)->toStartWith('http')
        ->and($url->url)->toEndWith('/v/'.$token)
        ->and($url->url)->toBe(rtrim((string) config('app.url'), '/').'/v/'.$token);

    $relative = (string) route('verification.public', ['token' => $token], absolute: false);

    expect($relative)->toBe('/v/'.$token);
});

test('the canonical URL is deterministic for the same token and unique across tokens', function (): void {
    $token = VerificationIdentityService::generateToken();
    $other = VerificationIdentityService::generateToken();

    $first = app(VerificationIdentityService::class)->verificationUrlForToken($token)->toString();
    $second = app(VerificationIdentityService::class)->verificationUrlForToken($token)->toString();

    expect($first)->toBe($second);

    $different = app(VerificationIdentityService::class)->verificationUrlForToken($other)->toString();

    expect($different)->not->toBe($first);
});

test('the canonical URL carries only the opaque token and never identifiers, PII or the digest', function (): void {
    $school = makeTenant();
    $document = AcademicDocument::factory()->forSchool($school)->create();
    $token = app(VerificationIdentityService::class)->issueForDocument($school, $document->getKey());
    $url = app(VerificationIdentityService::class)->verificationUrlForToken($token);

    $parts = parse_url($url->url);

    expect($parts['path'])->toBe('/v/'.$token)
        ->and($url->url)->not->toContain($document->document_number)
        ->and($url->url)->not->toContain($school)
        ->and($url->url)->not->toContain(VerificationIdentityService::hashToken($token))
        ->and($url->url)->not->toContain('academic_document')
        ->and($url->url)->not->toContain('?')
        ->and($url->url)->not->toContain('#');
});
