<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Enums;

/**
 * Forward-only lifecycle of an AcademicDocument (SPR-012).
 *
 * Draft → Generated → Reviewed → Approved → Published → Archived.
 *
 * Published versions are immutable: they can never be overwritten. Any
 * correction produces a new version of the same document (see DocumentStatus
 * and the versioning actions), never a mutation of the published record.
 * Documents whose publication policy does not require approval skip straight
 * to Published after Generated.
 */
enum DocumentStatus: string
{
    case Draft = 'draft';
    case Generated = 'generated';
    case Reviewed = 'reviewed';
    case Approved = 'approved';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Generated => 'Généré',
            self::Reviewed => 'Relu',
            self::Approved => 'Approuvé',
            self::Published => 'Publié',
            self::Archived => 'Archivé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Generated => 'info',
            self::Reviewed => 'warning',
            self::Approved => 'primary',
            self::Published => 'success',
            self::Archived => 'gray',
        };
    }

    public function isImmutable(): bool
    {
        return $this === self::Published || $this === self::Archived;
    }

    public function isPublished(): bool
    {
        return $this === self::Published;
    }

    /**
     * True when the engine is allowed to transition from $from to $this.
     */
    public function allowsTransitionFrom(self $from): bool
    {
        return match ($this) {
            self::Generated => $from === self::Draft,
            self::Reviewed => $from === self::Generated,
            self::Approved => $from === self::Generated || $from === self::Reviewed,
            self::Published => $from === self::Draft
                || $from === self::Generated
                || $from === self::Reviewed
                || $from === self::Approved,
            self::Archived => $from !== self::Archived && $from !== self::Draft,
            self::Draft => false,
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $status): string => $status->label(),
            self::cases(),
        );
    }
}
