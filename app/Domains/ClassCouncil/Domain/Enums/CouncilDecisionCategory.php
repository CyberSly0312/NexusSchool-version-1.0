<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Enums;

/**
 * Subject of a class council decision (SPR-013).
 *
 * The category classifies the decision for the rendered minutes and for the
 * downstream engines; it never carries business logic.
 */
enum CouncilDecisionCategory: string
{
    case Promotion = 'promotion';
    case Orientation = 'orientation';
    case Conduct = 'conduct';
    case Distinction = 'distinction';
    case General = 'general';

    public function label(): string
    {
        return match ($this) {
            self::Promotion => 'Promotion',
            self::Orientation => 'Orientation',
            self::Conduct => 'Conduite',
            self::Distinction => 'Distinction',
            self::General => 'Général',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $category): string => $category->label(),
            self::cases(),
        );
    }
}
