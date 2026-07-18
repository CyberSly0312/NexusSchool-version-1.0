<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Enums;

enum WidgetType: string
{
    case Chart = 'chart';
    case Metric = 'metric';
    case Table = 'table';
    case Progress = 'progress';
    case List = 'list';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Chart => 'Chart',
            self::Metric => 'Metric Card',
            self::Table => 'Data Table',
            self::Progress => 'Progress Bar',
            self::List => 'List',
            self::Custom => 'Custom Widget',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Chart => 'info',
            self::Metric => 'primary',
            self::Table => 'gray',
            self::Progress => 'success',
            self::List => 'warning',
            self::Custom => 'secondary',
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
