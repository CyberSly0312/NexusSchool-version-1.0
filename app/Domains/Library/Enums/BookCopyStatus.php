<?php

declare(strict_types=1);

namespace App\Domains\Library\Enums;

enum BookCopyStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Borrowed = 'borrowed';
    case Returned = 'returned';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponible',
            self::Reserved => 'Réservé',
            self::Borrowed => 'Emprunté',
            self::Returned => 'Retourné',
            self::Archived => 'Archivé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Available => 'success',
            self::Reserved => 'warning',
            self::Borrowed => 'info',
            self::Returned => 'gray',
            self::Archived => 'danger',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Available => in_array($target, [self::Reserved, self::Borrowed, self::Archived]),
            self::Reserved => in_array($target, [self::Available, self::Borrowed]),
            self::Borrowed => in_array($target, [self::Returned]),
            self::Returned => in_array($target, [self::Available, self::Borrowed, self::Archived]),
            self::Archived => false,
        };
    }
}
