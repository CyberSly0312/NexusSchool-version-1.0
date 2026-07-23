<?php

declare(strict_types=1);

namespace App\Domains\Library\Enums;

enum BookCopyCondition: string
{
    case New = 'new';
    case Good = 'good';
    case Fair = 'fair';
    case Poor = 'poor';
    case Damaged = 'damaged';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Neuf',
            self::Good => 'Bon',
            self::Fair => 'Acceptable',
            self::Poor => 'Mauvais',
            self::Damaged => 'Endommagé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'success',
            self::Good => 'success',
            self::Fair => 'warning',
            self::Poor => 'danger',
            self::Damaged => 'danger',
        };
    }
}
