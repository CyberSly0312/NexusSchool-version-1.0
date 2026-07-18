<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Enums;

enum SnapshotStatus: string
{
    case Pending = 'pending';
    case Generated = 'generated';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Generated => 'Generated',
            self::Failed => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Generated => 'success',
            self::Failed => 'danger',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases()),
        );
    }
}
