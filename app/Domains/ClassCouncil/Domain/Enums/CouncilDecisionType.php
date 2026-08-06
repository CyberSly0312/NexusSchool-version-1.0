<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Enums;

/**
 * Outcome of a class council decision (SPR-013).
 *
 * The council validates academic results and its decision is passed, failed or
 * reported. A decision may be flagged as an override of the automatic decision
 * derived from the published academic standing.
 */
enum CouncilDecisionType: string
{
    case Passed = 'passed';
    case Failed = 'failed';
    case Reported = 'reported';

    public function label(): string
    {
        return match ($this) {
            self::Passed => 'Admis',
            self::Failed => 'Redoublement',
            self::Reported => 'Signalé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Passed => 'success',
            self::Failed => 'danger',
            self::Reported => 'warning',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $type): string => $type->label(),
            self::cases(),
        );
    }
}
