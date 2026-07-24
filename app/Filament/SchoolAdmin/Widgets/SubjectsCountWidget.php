<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Widgets;

use App\Models\Subject;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubjectsCountWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $tenantId = tenant('id');
        $total = Subject::where('school_id', $tenantId)->count();
        $core = Subject::where('school_id', $tenantId)->where('is_core_subject', true)->count();
        $elective = Subject::where('school_id', $tenantId)->where('is_elective', true)->count();

        return [
            Stat::make('Matières', $total)
                ->description("{$core} principales, {$elective} optionnelles")
                ->descriptionIcon('heroicon-o-book-open')
                ->color('primary'),
        ];
    }
}
