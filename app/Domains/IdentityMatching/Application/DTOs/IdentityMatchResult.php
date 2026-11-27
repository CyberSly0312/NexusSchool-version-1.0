<?php

declare(strict_types=1);

namespace App\Domains\IdentityMatching\Application\DTOs;

use App\Domains\IdentityMatching\Domain\Enums\IdentityEvidenceType;
use App\Domains\IdentityMatching\Domain\Enums\IdentityMatchOutcome;
use App\Domains\IdentityMatching\Domain\Enums\IdentityMatchReasonCode;
use App\Domains\IdentityMatching\Domain\Enums\MatchableEntityType;

/**
 * Structured, privacy-safe result of identity resolution.
 *
 * The result never contains raw emails, phone numbers, names, tokens, hashes or
 * passwords. Candidate IDs are included only when there is exactly one
 * in-school candidate. `candidateFound` distinguishes the conservative
 * no-candidate result from a real possible candidate without inventing a
 * fourth public outcome.
 */
final class IdentityMatchResult
{
    public readonly IdentityMatchOutcome $outcome;

    public readonly MatchableEntityType $entityType;

    public readonly int|string|null $candidateId;

    /** @var list<IdentityEvidenceType> */
    public readonly array $evidence;

    /** @var list<IdentityMatchReasonCode> */
    public readonly array $reasonCodes;

    public readonly bool $automaticReusePermitted;

    public readonly bool $candidateFound;

    /**
     * @param  list<IdentityEvidenceType>  $evidence
     * @param  list<IdentityMatchReasonCode>  $reasonCodes
     */
    public function __construct(
        IdentityMatchOutcome $outcome,
        MatchableEntityType $entityType,
        int|string|null $candidateId,
        array $evidence,
        array $reasonCodes,
        bool $automaticReusePermitted,
        bool $candidateFound = false,
    ) {
        $this->outcome = $outcome;
        $this->entityType = $entityType;
        $this->candidateId = $candidateId;
        $this->evidence = self::uniqueEvidence($evidence);
        $this->reasonCodes = self::uniqueReasons($reasonCodes);
        $this->automaticReusePermitted = $automaticReusePermitted;
        $this->candidateFound = $candidateFound;
    }

    /**
     * @param  list<IdentityEvidenceType>  $evidence
     * @return list<IdentityEvidenceType>
     */
    private static function uniqueEvidence(array $evidence): array
    {
        $unique = [];

        foreach ($evidence as $item) {
            $unique[$item->value] = $item;
        }

        return array_values($unique);
    }

    /**
     * @param  list<IdentityMatchReasonCode>  $reasonCodes
     * @return list<IdentityMatchReasonCode>
     */
    private static function uniqueReasons(array $reasonCodes): array
    {
        $unique = [];

        foreach ($reasonCodes as $reason) {
            $unique[$reason->value] = $reason;
        }

        return array_values($unique);
    }
}
