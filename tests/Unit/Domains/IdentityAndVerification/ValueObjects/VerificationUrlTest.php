<?php

declare(strict_types=1);

use App\Domains\IdentityAndVerification\Domain\ValueObjects\VerificationUrl;

/**
 * Canonical verification URL composition (SPR-016 Phase 5, ADR-022 Decision 5).
 *
 * The URL is a pure function of the opaque token and the canonical base: it
 * carries only the token inside /v/{token}, is absolute, deterministic and
 * normalized (no double slashes, no stray separators), and is constructed from
 * a URL-safe base64url token only.
 */
test('compose builds the canonical absolute verification URL as {base}/v/{token}', function (): void {
    $url = VerificationUrl::compose('Ab1_cd2-EF', 'https://verify.example.com', '/v/Ab1_cd2-EF');

    expect($url->toString())->toBe('https://verify.example.com/v/Ab1_cd2-EF')
        ->and($url->token)->toBe('Ab1_cd2-EF');
});

test('compose normalizes a trailing base slash and a missing leading path slash', function (): void {
    $token = 'xYz_-012';

    $withTrailingSlash = VerificationUrl::compose($token, 'https://verify.example.com/', '/v/'.$token);
    $withLeadingSlash = VerificationUrl::compose($token, 'https://verify.example.com', 'v/'.$token);
    $withSpacedPath = VerificationUrl::compose($token, 'https://verify.example.com', ' v/'.$token);

    expect($withTrailingSlash->toString())->toBe('https://verify.example.com/v/'.$token)
        ->and($withLeadingSlash->toString())->toBe('https://verify.example.com/v/'.$token)
        ->and($withSpacedPath->toString())->toBe('https://verify.example.com/v/'.$token);
});

test('compose never produces a double slash between base and path', function (): void {
    $url = VerificationUrl::compose('aBc_-dEf', 'https://verify.example.com/', '/v/aBc_-dEf');

    expect($url->toString())->not->toContain('//v/')
        ->and($url->toString())->toBe('https://verify.example.com/v/aBc_-dEf');
});

test('the same token and base always compose the same URL', function (): void {
    $token = 'sameToken_012';

    $first = VerificationUrl::compose($token, 'https://verify.example.com', '/v/'.$token);
    $second = VerificationUrl::compose($token, 'https://verify.example.com', '/v/'.$token);

    expect($first->toString())->toBe($second->toString());
});

test('different tokens compose different URLs', function (): void {
    $urlA = VerificationUrl::compose('tokenA_-01', 'https://verify.example.com', '/v/tokenA_-01');
    $urlB = VerificationUrl::compose('tokenB_-02', 'https://verify.example.com', '/v/tokenB_-02');

    expect($urlA->toString())->not->toBe($urlB->toString());
});

test('compose rejects tokens that are not URL-safe base64url', function (): void {
    foreach (['', 'has a space', 'dot.notation', 'slash/inside', 'plus+plus', str_repeat('A', 129)] as $token) {
        expect(fn (): VerificationUrl => VerificationUrl::compose($token, 'https://verify.example.com', '/v/'.$token))
            ->toThrow(InvalidArgumentException::class);
    }
});

test('the composed URL carries only the token path segment and the base', function (): void {
    $token = 'pIvhU_sT-token';
    $url = VerificationUrl::compose($token, 'https://verify.example.com', '/v/'.$token);

    expect($url->url)->toMatch('/^https:\/\/verify\.example\.com\/v\/[A-Za-z0-9_-]+$/')
        ->and($url->url)->toBe('https://verify.example.com/v/'.$token)
        ->and($url->url)->not->toContain('school')
        ->and($url->url)->not->toContain('document')
        ->and($url->url)->not->toContain('tenant')
        ->and($url->url)->not->toContain('?')
        ->and($url->url)->not->toContain('#')
        ->and($url->url)->not->toContain(hash('sha256', $token));
});
