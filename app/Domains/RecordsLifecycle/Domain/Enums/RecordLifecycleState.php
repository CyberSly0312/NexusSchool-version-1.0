<?php

declare(strict_types=1);

namespace App\Domains\RecordsLifecycle\Domain\Enums;

enum RecordLifecycleState: string
{
    case Registered = 'registered';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Registered => 'Registered',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }

    public function allowsTransitionFrom(self $from): bool
    {
        return match ([$from, $this]) {
            [self::Registered, self::Published],
            [self::Registered, self::Archived],
            [self::Published, self::Archived] => true,
            default => false,
        };
    }
}
