<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Application\DTOs;

use App\Domains\IdentityAndVerification\Domain\Enums\VerificationEligibilityVerdict;
use App\Domains\IdentityAndVerification\Domain\References\DocumentVerificationState;

/**
 * Public verification response for a single token lookup (SPR-016 Phase 3,
 * ADR-022 Decision 3 / LEVEL-2 response contract).
 *
 * Holds exactly the LEVEL-2 fields the public surface may answer with. Fields
 * that are not applicable to a verdict are left null and are dropped by the
 * PublicVerificationPolicy (never partially filled). The verdict is carried as
 * the enum so the policy remains the single authority on the surface strings;
 * the DTO itself never renders to JSON directly.
 */
final class PublicVerificationResponseData
{
    public function __construct(
        public readonly VerificationEligibilityVerdict $verdict,
        public readonly ?string $documentNumber = null,
        public readonly ?string $documentType = null,
        public readonly ?string $status = null,
        public readonly ?string $issuedAt = null,
    ) {
    }

    public static function noPublicRecord(): self
    {
        return new self(
            verdict: VerificationEligibilityVerdict::NotPubliclyVerifiable,
        );
    }

    public static function fromDocumentState(
        VerificationEligibilityVerdict $verdict,
        DocumentVerificationState $document,
        string $status,
        ?string $issuedAt = null,
    ): self {
        return new self(
            verdict: $verdict,
            documentNumber: $document->documentNumber,
            documentType: $document->documentType,
            status: $status,
            issuedAt: $issuedAt,
        );
    }

    /**
     * Candidate payload for the PublicVerificationPolicy filter.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = ['verdict' => $this->verdict];

        $fields = [
            'document_number' => $this->documentNumber,
            'document_type' => $this->documentType,
            'status' => $this->status,
            'issued_at' => $this->issuedAt,
        ];

        foreach ($fields as $key => $value) {
            if ($value !== null) {
                $payload[$key] = $value;
            }
        }

        return $payload;
    }
}
