<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StaffCountWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $total = User::where('school_id', tenant('id'))->count();
        $teachers = User::where('school_id', tenant('id'))
            ->whereHas('roles', fn ($q) => $q->where('name', 'teacher'))
            ->count();
        $staff = $total - $teachers;

        return [
            Stat::make('Personnel', $staff)
                ->description("{$teachers} enseignants, {$total} total")
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),
        ];
    }
}
