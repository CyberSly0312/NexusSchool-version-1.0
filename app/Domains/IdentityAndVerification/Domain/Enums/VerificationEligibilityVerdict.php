<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\Enums;

/**
 * Public verification verdict for a document (SPR-016, ADR-022 Decision 7).
 *
 * For active identities the verdict is computed exclusively from the document
 * lifecycle produced by the Academic Communication substrate; SPR-016 never
 * owns a second document lifecycle engine. A verification identity that has
 * been officially revoked or replaced takes precedence over the document
 * lifecycle: those states are never collapsed into the archived verdict.
 */
enum VerificationEligibilityVerdict: string
{
    case NotPubliclyVerifiable = 'not_publicly_verifiable';
    case AuthenticPublished = 'authentic_published';
    case AuthenticArchived = 'authentic_archived';
    case AuthenticOfficiallyRevoked = 'authentic_officially_revoked';
    case AuthenticSuperseded = 'authentic_superseded';
}
