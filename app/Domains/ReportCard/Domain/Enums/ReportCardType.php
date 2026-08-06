<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Enums;

/**
 * Kind of a report card run (SPR-011).
 */
enum ReportCardType: string
{
    case Term = 'term';
    case Annual = 'annual';

    public function label(): string
    {
        return match ($this) {
            self::Term => 'Bulletin trimestriel',
            self::Annual => 'Bulletin annuel',
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
