<?php

declare(strict_types=1);

namespace App\Domains\IdentityMatching\Domain\Enums;

/**
 * The only three public outcomes of school-scoped identity resolution.
 *
 * EXACT_MATCH is the only outcome that may enable automated reuse, and even
 * then the caller must recheck its own authorization and lifecycle rules.
 * POSSIBLE_MATCH is deliberately non-authoritative. AMBIGUOUS_OR_CONFLICT
 * stops automation without exposing any foreign-school information.
 */
enum IdentityMatchOutcome: string
{
    case ExactMatch = 'EXACT_MATCH';
    case PossibleMatch = 'POSSIBLE_MATCH';
    case AmbiguousOrConflict = 'AMBIGUOUS_OR_CONFLICT';
}
