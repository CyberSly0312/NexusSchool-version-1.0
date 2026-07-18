<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Enums;

enum DashboardType: string
{
    case School = 'school';
    case Academic = 'academic';
    case Finance = 'finance';
    case HR = 'hr';
    case Library = 'library';
    case Transport = 'transport';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::School => 'School Overview',
            self::Academic => 'Academic Dashboard',
            self::Finance => 'Finance Dashboard',
            self::HR => 'HR Dashboard',
            self::Library => 'Library Dashboard',
            self::Transport => 'Transport Dashboard',
            self::Custom => 'Custom Dashboard',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::School => 'primary',
            self::Academic => 'info',
            self::Finance => 'success',
            self::HR => 'warning',
            self::Library => 'gray',
            self::Transport => 'danger',
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
