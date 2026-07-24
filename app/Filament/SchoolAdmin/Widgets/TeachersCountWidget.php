<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeachersCountWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $total = User::where('school_id', tenant('id'))
            ->whereHas('roles', fn ($q) => $q->where('name', 'teacher'))
            ->count();

        $active = User::where('school_id', tenant('id'))
            ->whereHas('roles', fn ($q) => $q->where('name', 'teacher'))
            ->where('status', 'active')
            ->count();

        return [
            Stat::make('Enseignants', $total)
                ->description("{$active} actif(s)")
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('info')
                ->chart([$total - 2, $total - 1, $total]),
        ];
    }
}
