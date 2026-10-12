<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\ValueObjects;

use App\Domains\IdentityAndVerification\Domain\Enums\VerificationEligibilityVerdict;

/**
 * Minimal privacy contract of the public verification surface (SPR-016 Phase 3,
 * ADR-022 Decision 3 / LEVEL-2).
 *
 * The ONLY fields the surface may expose are whitelisted here. Every public
 * response is routed through filter(), so a field added to the response DTO can
 * never reach an HTTP response unless it is explicitly allowed by this policy.
 * Forbidden signals (grades, content, student data, tenant identifiers, the
 * token itself) are structurally absent from the documented surface.
 *
 * Document numbers and document types are public document identifiers, not
 * secrets; they may be revealed only while the verdict is informative.
 */
final class PublicVerificationPolicy
{
    public const SURFACE_STRING = 'no_public_record';

    /**
     * Fields allowed in a public verification response (ADR-022 LEVEL-2).
     *
     * @var array<string, true>
     */
    private const ALLOWED_FIELDS = [
        'verdict' => true,
        'document_number' => true,
        'document_type' => true,
        'status' => true,
        'issued_at' => true,
    ];

    /**
     * Reduce a candidate payload to the whitelisted public fields and
     * canonicalize the verdict surface string.
     *
     * @param  array<string, mixed>  $candidate
     * @return array<string, mixed>
     */
    public function filter(array $candidate): array
    {
        $payload = [];

        foreach ($candidate as $key => $value) {
            if (! isset(self::ALLOWED_FIELDS[$key]) || $value === null) {
                continue;
            }

            if ($key === 'verdict') {
                $payload[$key] = $this->surfaceVerdict($value);

                continue;
            }

            $payload[$key] = $value;
        }

        return $payload;
    }

    /**
     * The non-revealing uniform verdict string used whenever no public record
     * may be acknowledged (unknown, malformed, unpublished, missing document).
     */
    public function noPublicRecord(): string
    {
        return self::SURFACE_STRING;
    }

    private function surfaceVerdict(mixed $verdict): string
    {
        if ($verdict instanceof VerificationEligibilityVerdict) {
            return $verdict === VerificationEligibilityVerdict::NotPubliclyVerifiable
                ? self::SURFACE_STRING
                : $verdict->value;
        }

        return (string) $verdict;
    }
}
