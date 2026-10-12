<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Presentation\Http;

use App\Domains\IdentityAndVerification\Application\Services\VerificationIdentityService;
use App\Domains\IdentityAndVerification\Domain\ValueObjects\PublicVerificationPolicy;
use Illuminate\Http\JsonResponse;

/**
 * Public verification endpoint (SPR-016 Phase 3, ADR-022 Decision 3).
 *
 * Thin presentation: resolves a token through the application service and
 * filters the payload through the privacy policy. The endpoint is intentionally
 * unauthenticated and read-only (rate-limited by the named limiter registered
 * in the owning provider). It never logs or echoes the plaintext token.
 */
final class PublicVerificationController
{
    public function __invoke(
        string $token,
        VerificationIdentityService $identities,
        PublicVerificationPolicy $policy,
    ): JsonResponse {
        $payload = $policy->filter($identities->publicVerification($token)->toArray());

        return response()->json($payload, status: 200);
    }
}
