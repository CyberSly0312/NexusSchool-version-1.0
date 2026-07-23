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
            self::School => 'Vue d\'ensemble de l\'école',
            self::Academic => 'Tableau de bord académique',
            self::Finance => 'Tableau de bord financier',
            self::HR => 'Tableau de bord RH',
            self::Library => 'Tableau de bord bibliothèque',
            self::Transport => 'Tableau de bord transport',
            self::Custom => 'Tableau de bord personnalisé',
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
