<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Widgets;

use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentsCountWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenantId = tenant('id');
        $total = Student::where('school_id', $tenantId)->count();
        $active = Student::where('school_id', $tenantId)->where('status', 'active')->count();

        return [
            Stat::make('Élèves', $total)
                ->description("{$active} actif(s)")
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary')
                ->chart([$total - 3, $total - 1, $total]),
        ];
    }
}
