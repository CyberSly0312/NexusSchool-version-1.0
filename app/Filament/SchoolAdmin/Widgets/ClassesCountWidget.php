<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Widgets;

use App\Models\AcademicClass;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClassesCountWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $tenantId = tenant('id');
        $total = AcademicClass::where('school_id', $tenantId)->count();
        $active = AcademicClass::where('school_id', $tenantId)->where('is_active', true)->count();

        return [
            Stat::make('Classes', $total)
                ->description("{$active} active(s)")
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('warning')
                ->chart([$total - 1, $total]),
        ];
    }
}
