<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Enums;

/**
 * Broad grouping of a configurable relationship type. Used to categorize the
 * catalog (family ties, legal responsibility, financial support, emergency
 * reachability, anything else) without hard-coding the types themselves.
 */
enum RelationshipCategory: string
{
    case Family = 'family';
    case Legal = 'legal';
    case Financial = 'financial';
    case Emergency = 'emergency';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Family => 'Family',
            self::Legal => 'Legal',
            self::Financial => 'Financial',
            self::Emergency => 'Emergency',
            self::Other => 'Other',
        };
    }
}
