<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Canonical verification URL of an opaque verification token (SPR-016 Phase 5,
 * ADR-022 Decision 5).
 *
 * The payload contract consumed by QR/document-rendering infrastructure is
 * exactly ``{base}/v/{token}``: an absolute, deterministic URL carrying the
 * opaque token and nothing else. The URL is a pure function of the token and
 * the canonical base — never of the document number, school, tenant, document
 * id or student. Because the token is immutable for the life of the document
 * aggregate (ADR-022 I8), the same token always yields the same URL; the URL
 * therefore survives regeneration, versioning, archival, revocation and
 * replacement unchanged.
 *
 * This value object only composes and normalizes the canonical form; it never
 * resolves, stores or logs the token. Construction requires the plaintext
 * token — the identity record persists only its SHA-256 digest (ADR-022 I11),
 * which can never be reversed. A consumer holding the token (for example the
 * issuing school at the issuance boundary, or a printed/QR representation)
 * rebuilds the canonical URL deterministically.
 */
final readonly class VerificationUrl
{
    /**
     * The URL-safe base64url form produced by VerificationIdentityService::generateToken().
     */
    public const TOKEN_PATTERN = '/^[A-Za-z0-9_-]{1,128}$/';

    private function __construct(
        public string $token,
        public string $url,
    ) {
    }

    /**
     * Compose the canonical absolute verification URL from a token, the
     * canonical base URL and the canonical route path.
     *
     * Only URL-safe base64url tokens are accepted so the composed URL stays
     * canonical (no percent-encoding drift) and deterministic.
     */
    public static function compose(string $token, string $baseUrl, string $path): self
    {
        if (preg_match(self::TOKEN_PATTERN, $token) !== 1) {
            throw new InvalidArgumentException('The opaque verification token must be URL-safe base64url (letters, digits, - and _).');
        }

        $baseUrl = rtrim($baseUrl, '/');
        $path = '/'.ltrim(trim($path), '/');

        return new self($token, $baseUrl.$path);
    }

    public function toString(): string
    {
        return $this->url;
    }

    public function __toString(): string
    {
        return $this->url;
    }
}
