<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Enums;

/**
 * Approval state of an AcademicDocument (SPR-012).
 *
 * A document whose publication policy requires approval moves through
 * Pending → Approved (or Rejected). Documents that do not require approval
 * keep the NotRequired state for the whole lifecycle.
 */
enum ApprovalStatus: string
{
    case NotRequired = 'not_required';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::NotRequired => 'Non requise',
            self::Pending => 'En attente',
            self::Approved => 'Approuvée',
            self::Rejected => 'Rejetée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NotRequired => 'gray',
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
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
