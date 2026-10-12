<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\Enums;

/**
 * Verification identity status (SPR-016, ADR-022 Decision 4).
 *
 * The minimum state representation of a verification identity. It is distinct
 * from the document lifecycle: DocumentStatus remains the authoritative
 * lifecycle of an AcademicDocument (ADR-022 Decision 7) and is never duplicated
 * here. This enum is a small controlled value set decoupled from token hashing.
 */
enum VerificationStatus: string
{
    case Active = 'active';
    case Revoked = 'revoked';
    case Replaced = 'replaced';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Revoked => 'Revoked',
            self::Replaced => 'Replaced',
        };
    }

    /**
     * Presentation colour token shared by every read-only surface that renders
     * a verification status (I&V management resource and the AcademicDocument
     * verification visibility). Keeps the rendering mapping in one place.
     */
    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Revoked => 'danger',
            self::Replaced => 'warning',
        };
    }

    /**
     * Explicit forward-only state machine (ADR-022 Decision 4).
     *
     * Active is the only entry state. Revoked and Replaced are terminal: a
     * verification identity can never be re-activated, re-revoked or converted
     * after an invalidation. Replaced supersedes Revoked for the same identity;
     * both are reachable exclusively from Active.
     */
    public function allowsTransitionFrom(self $from): bool
    {
        return match ($this) {
            self::Revoked, self::Replaced => $from === self::Active,
            self::Active => false,
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $status): string => $status->label(),
            self::cases(),
        );
    }
}
