<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Application\Services;

use App\Domains\IdentityAndVerification\Application\DTOs\PublicVerificationResponseData;
use App\Domains\IdentityAndVerification\Application\DTOs\VerificationIdentityDetailData;
use App\Domains\IdentityAndVerification\Domain\Contracts\AcademicDocumentVerificationContract;
use App\Domains\IdentityAndVerification\Domain\Enums\VerificationEligibilityVerdict;
use App\Domains\IdentityAndVerification\Domain\Enums\VerificationStatus;
use App\Domains\IdentityAndVerification\Domain\Events\VerificationIdentityReplaced;
use App\Domains\IdentityAndVerification\Domain\Events\VerificationIdentityRevoked;
use App\Domains\IdentityAndVerification\Domain\Exceptions\InvalidVerificationTransitionException;
use App\Domains\IdentityAndVerification\Domain\Exceptions\VerificationDocumentNotFoundException;
use App\Domains\IdentityAndVerification\Domain\Exceptions\VerificationDocumentNotPubliclyVerifiableException;
use App\Domains\IdentityAndVerification\Domain\Exceptions\VerificationIdentityAlreadyExistsException;
use App\Domains\IdentityAndVerification\Domain\Exceptions\VerificationIdentityNotFoundException;
use App\Domains\IdentityAndVerification\Domain\Exceptions\VerificationReasonRequiredException;
use App\Domains\IdentityAndVerification\Domain\References\DocumentVerificationState;
use App\Domains\IdentityAndVerification\Domain\ValueObjects\VerificationUrl;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * Verification identity issuance and lifecycle (SPR-016 "token generation API"
 * and Phase 4 revocation / replacement, ADR-022 Decision 2/4).
 *
 * issueForDocument() binds one identity to an AcademicDocument, persists only
 * the SHA-256 digest of the token and returns the plaintext exactly once.
 * resolve() performs the inverse operation (plaintext → digest → record), so
 * the plaintext never acts as a stored key. The plaintext token is never
 * logged. One identity per document is enforced structurally by the
 * academic_document_id unique constraint (ADR-022 Decision 8).
 *
 * revoke() and replaceWith() are the single sanctioned mutation path of a
 * verification identity (ADR-022 Decision 4): both run inside a transaction,
 * require a principal holding the document.revoke capability, enforce the
 * forward-only terminal state machine and only ever apply to documents that
 * are publicly verifiable (published or archived) — so a revocation can never
 * leak the existence of an unpublished document. The recorded reason is
 * internal (identity + audit) and never reaches the public surface.
 */
final class VerificationIdentityService
{
    public function __construct(
        private readonly AcademicDocumentVerificationContract $documents,
        private readonly VerificationEligibility $eligibility,
    ) {
    }

    public function issueForDocument(string $schoolId, int|string $documentId): string
    {
        $document = $this->documents->stateForDocument($schoolId, $documentId);

        if ($document === null) {
            throw VerificationDocumentNotFoundException::forSchoolAndDocument($schoolId, $documentId);
        }

        $alreadyIssued = VerificationIdentity::query()
            ->forSchool($schoolId)
            ->where('academic_document_id', $document->documentId)
            ->exists();

        if ($alreadyIssued) {
            throw VerificationIdentityAlreadyExistsException::forDocument($document->documentId);
        }

        $token = self::generateToken();
        $hash = self::hashToken($token);

        DB::transaction(function () use ($schoolId, $document, $hash): void {
            VerificationIdentity::create([
                'school_id' => $schoolId,
                'academic_document_id' => $document->documentId,
                'token_hash' => $hash,
                'status' => VerificationStatus::Active,
                'issued_at' => now(),
            ]);
        });

        return $token;
    }

    public function resolve(string $plaintextToken): ?VerificationIdentityDetailData
    {
        $identity = VerificationIdentity::query()
            ->where('token_hash', self::hashToken($plaintextToken))
            ->first();

        if ($identity === null) {
            return null;
        }

        $document = $this->documents->stateForDocument($identity->school_id, $identity->academic_document_id);

        if ($document === null) {
            return null;
        }

        $verdict = $this->eligibility->verdictForDocumentStatus($document->documentStatus);

        return new VerificationIdentityDetailData(
            identityUuid: (string) $identity->uuid,
            schoolId: $identity->school_id,
            documentId: $identity->academic_document_id,
            status: $identity->status->value,
            documentStatus: $document->documentStatus,
            verdict: $verdict,
            publiclyVerifiable: $verdict !== VerificationEligibilityVerdict::NotPubliclyVerifiable,
            issuedAt: $identity->issued_at,
        );
    }

    /**
     * Public, unauthenticated verification lookup (SPR-016 Phase 3,
     * ADR-022 Decision 3 / LEVEL-2).
     *
     * Answers the uniform non-revealing response for unknown, malformed,
     * unpublished or missing documents. The digest of the token is always
     * computed before the format guard so invalid inputs cost the same work as
     * valid ones, and the plaintext token never reaches the database, cache or
     * logs. Verified states are never collapsed: an identity that was officially
     * revoked or replaced takes precedence over the document lifecycle.
     */
    public function publicVerification(string $plaintextToken): PublicVerificationResponseData
    {
        $hash = self::hashToken($plaintextToken);

        if (preg_match('/^[A-Za-z0-9_-]{1,128}$/', $plaintextToken) !== 1) {
            return PublicVerificationResponseData::noPublicRecord();
        }

        $identity = VerificationIdentity::query()
            ->where('token_hash', $hash)
            ->first();

        if ($identity === null) {
            return PublicVerificationResponseData::noPublicRecord();
        }

        $document = $this->documents->stateForDocument($identity->school_id, $identity->academic_document_id);

        if ($document === null) {
            return PublicVerificationResponseData::noPublicRecord();
        }

        if ($identity->status === VerificationStatus::Revoked) {
            return PublicVerificationResponseData::fromDocumentState(
                verdict: VerificationEligibilityVerdict::AuthenticOfficiallyRevoked,
                document: $document,
                status: VerificationStatus::Revoked->value,
            );
        }

        if ($identity->status === VerificationStatus::Replaced) {
            return PublicVerificationResponseData::fromDocumentState(
                verdict: VerificationEligibilityVerdict::AuthenticSuperseded,
                document: $document,
                status: VerificationStatus::Replaced->value,
            );
        }

        $verdict = $this->eligibility->verdictForDocumentStatus($document->documentStatus);

        if ($verdict === VerificationEligibilityVerdict::NotPubliclyVerifiable) {
            return PublicVerificationResponseData::noPublicRecord();
        }

        return PublicVerificationResponseData::fromDocumentState(
            verdict: $verdict,
            document: $document,
            status: $document->documentStatus,
            issuedAt: $document->generatedAt,
        );
    }

    /**
     * Canonical verification URL for a held plaintext token (SPR-016 Phase 5,
     * ADR-022 Decision 5).
     *
     * The URL is built from the canonical application URL (config('app.url'))
     * and the relative path of the existing named public verification route,
     * so it is absolute, deterministic and tenant-host-independent — a token
     * handed over inside a school portal context never inherits that school's
     * domain. The token is the only secret-bearing component and is not
     * persisted, logged or echoed back; the identity record itself can never
     * produce the URL because it stores only the SHA-256 digest (I11). A
     * consumer that already holds the token rebuilds the same URL at any point
     * in the aggregate life, unchanged by regeneration, versioning, archival,
     * revocation or replacement.
     */
    public function verificationUrlForToken(string $token): VerificationUrl
    {
        $baseUrl = (string) config('app.url');
        $path = (string) route('verification.public', ['token' => $token], absolute: false);

        return VerificationUrl::compose($token, $baseUrl, $path);
    }

    /**
     * Officially revoke a verification identity (SPR-016 Phase 4,
     * ADR-022 Decision 4).
     *
     * Forward-only from Active to Revoked; irreversible. Requires a principal
     * holding the document.revoke capability for the owning school, a
     * meaningful free-text reason, and a document that is publicly verifiable
     * (published or archived). The token and its digest are preserved so the
     * historical record stays resolvable as authentic_officially_revoked.
     */
    public function revoke(
        string $schoolId,
        int|string $documentId,
        string $reason,
        ?User $actor = null,
    ): VerificationIdentity {
        $reason = $this->requireReason($reason, 'revoke');

        return DB::transaction(function () use ($schoolId, $documentId, $reason, $actor): VerificationIdentity {
            $identity = $this->requireIdentityForDocument($schoolId, $documentId);
            $this->requirePubliclyRevocableDocument($schoolId, $documentId);

            $this->authorizeLifecycle($actor, $identity, 'revoke');

            $identity->assertCanTransitionTo(VerificationStatus::Revoked);

            $identity->update([
                'status' => VerificationStatus::Revoked,
                'revocation_reason' => $reason,
                'revoked_at' => now(),
            ]);

            $identity = $identity->refresh();

            VerificationIdentityRevoked::dispatch($identity, $reason, $actor);

            return $identity;
        });
    }

    /**
     * Mark a verification identity as replaced by a successor document
     * (SPR-016 Phase 4, ADR-022 Decision 4).
     *
     * Forward-only from Active to Replaced and irreversible. The successor must
     * belong to the same school (the contract returns null across schools →
     * fail closed) and must differ from the superseded document. The successor
     * receives its own identity through the existing issuance flow; this
     * method only records the replacement link and keeps the superseded
     * identity historically resolvable as authentic_superseded.
     */
    public function replaceWith(
        string $schoolId,
        int|string $documentId,
        int|string $successorDocumentId,
        string $reason,
        ?User $actor = null,
    ): VerificationIdentity {
        $reason = $this->requireReason($reason, 'replace');

        return DB::transaction(function () use ($schoolId, $documentId, $successorDocumentId, $reason, $actor): VerificationIdentity {
            $identity = $this->requireIdentityForDocument($schoolId, $documentId);
            $this->requirePubliclyRevocableDocument($schoolId, $documentId);

            $successor = $this->documents->stateForDocument($schoolId, $successorDocumentId);

            if ($successor === null) {
                throw VerificationDocumentNotFoundException::forSchoolAndDocument($schoolId, $successorDocumentId);
            }

            if ((string) $successor->documentId === (string) $documentId) {
                throw InvalidVerificationTransitionException::selfReplacement($documentId);
            }

            $this->authorizeLifecycle($actor, $identity, 'replace');

            $identity->assertCanTransitionTo(VerificationStatus::Replaced);

            $identity->update([
                'status' => VerificationStatus::Replaced,
                'revocation_reason' => $reason,
                'replaced_at' => now(),
                'replaced_by_document_number' => $successor->documentNumber,
            ]);

            $identity = $identity->refresh();

            VerificationIdentityReplaced::dispatch(
                $identity,
                $successor->documentId,
                $successor->documentNumber,
                $reason,
                $actor,
            );

            return $identity;
        });
    }

    private function requireReason(string $reason, string $transition): string
    {
        $reason = trim($reason);

        if ($reason === '') {
            throw VerificationReasonRequiredException::forTransition($transition);
        }

        return $reason;
    }

    private function requireIdentityForDocument(string $schoolId, int|string $documentId): VerificationIdentity
    {
        $identity = VerificationIdentity::query()
            ->forSchool($schoolId)
            ->where('academic_document_id', $documentId)
            ->first();

        if ($identity === null) {
            throw VerificationIdentityNotFoundException::forDocument($schoolId, $documentId);
        }

        return $identity;
    }

    /**
     * Guard I1: only publicly verifiable documents (published or archived) may
     * be revoked or replaced. Allowing the invalidation of an unpublished
     * document would let its token answer authentic_officially_revoked /
     * authentic_superseded, leaking an existence that must never be acknowledged.
     */
    private function requirePubliclyRevocableDocument(string $schoolId, int|string $documentId): DocumentVerificationState
    {
        $document = $this->documents->stateForDocument($schoolId, $documentId);

        if ($document === null) {
            throw VerificationDocumentNotFoundException::forSchoolAndDocument($schoolId, $documentId);
        }

        if (! $this->eligibility->isPubliclyVerifiable($document->documentStatus)) {
            throw VerificationDocumentNotPubliclyVerifiableException::forDocument($document->documentId, $document->documentStatus);
        }

        return $document;
    }

    private function authorizeLifecycle(?User $actor, VerificationIdentity $identity, string $ability): void
    {
        if ($actor === null) {
            throw new AuthorizationException('A principal is required to invalidate a verification identity.');
        }

        Gate::forUser($actor)->authorize($ability, $identity);
    }

    /**
     * Generate a typeable opaque verification token (≥ 144 bits, ADR-022
     * Decision 2): 24 CSPRNG bytes, base64url-encoded.
     */
    public static function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(24)), '+/', '-_'), '=');
    }

    /**
     * Complete SHA-256 hexadecimal digest of a verification token (64 chars).
     * The digest is the only persisted representation of the token.
     */
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }
}
