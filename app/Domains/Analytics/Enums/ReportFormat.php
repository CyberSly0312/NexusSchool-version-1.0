<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Enums;

enum ReportFormat: string
{
    case Pdf = 'pdf';
    case Excel = 'excel';
    case Csv = 'csv';

    public function label(): string
    {
        return match ($this) {
            self::Pdf => 'PDF',
            self::Excel => 'Excel',
            self::Csv => 'CSV',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pdf => 'danger',
            self::Excel => 'success',
            self::Csv => 'info',
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
