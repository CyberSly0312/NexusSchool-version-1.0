<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Failed = 'failed';
    case Skipped = 'skipped';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Assigned => 'Assigné',
            self::InProgress => 'En cours',
            self::Completed => 'Terminé',
            self::Failed => 'Échoué',
            self::Skipped => 'Ignoré',
            self::Cancelled => 'Annulé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Assigned => 'info',
            self::InProgress => 'warning',
            self::Completed => 'success',
            self::Failed => 'danger',
            self::Skipped => 'secondary',
            self::Cancelled => 'secondary',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases()),
        );
    }

    public static function colors(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->color(), self::cases()),
        );
    }

    public function isActive(): bool
    {
        return in_array($this, [self::Pending, self::Assigned, self::InProgress]);
    }
}
