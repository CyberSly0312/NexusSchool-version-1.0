<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Enums;

enum AssessmentResultStatus: string
{
    case Draft = 'draft';
    case Entered = 'entered';
    case Verified = 'verified';
    case Published = 'published';
    case Locked = 'locked';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Entered => 'Saisi',
            self::Verified => 'Vérifié',
            self::Published => 'Publié',
            self::Locked => 'Verrouillé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Entered => 'info',
            self::Verified => 'success',
            self::Published => 'primary',
            self::Locked => 'danger',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Draft => in_array($target, [self::Draft, self::Entered]),
            self::Entered => in_array($target, [self::Entered, self::Verified, self::Draft]),
            self::Verified => in_array($target, [self::Verified, self::Published, self::Entered]),
            self::Published => in_array($target, [self::Published, self::Locked, self::Verified]),
            self::Locked => false,
        };
    }

    public function isReadOnly(): bool
    {
        return $this === self::Locked || $this === self::Published;
    }

    public function isDeletable(): bool
    {
        return in_array($this, [self::Draft, self::Entered]);
    }
}
