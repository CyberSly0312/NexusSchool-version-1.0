<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Widgets;

use App\Models\AcademicYear;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActiveAcademicYearWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 7;

    protected function getStats(): array
    {
        $activeYear = AcademicYear::where('school_id', tenant('id'))
            ->where('status', 'active')
            ->first();

        $totalYears = AcademicYear::where('school_id', tenant('id'))->count();

        if ($activeYear) {
            $termsCount = $activeYear->academicTerms()->count();
            $activeTerms = $activeYear->academicTerms()->where('status', 'active')->count();

            return [
                Stat::make('Année active', $activeYear->name)
                    ->description("{$activeTerms}/{$termsCount} périodes actives")
                    ->descriptionIcon('heroicon-o-calendar-days')
                    ->color('success'),
            ];
        }

        return [
            Stat::make('Année active', 'Aucune')
                ->description("{$totalYears} année(s) configurée(s)")
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('danger'),
        ];
    }
}
