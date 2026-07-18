<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Enums;

enum ReportStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Generating = 'generating';
    case Completed = 'completed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Scheduled => 'Scheduled',
            self::Generating => 'Generating',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Scheduled => 'info',
            self::Generating => 'warning',
            self::Completed => 'success',
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
