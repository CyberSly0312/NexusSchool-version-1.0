<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Widgets;

use App\Domains\Academic\Application\Services\CalendarService;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class CalendarOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 6;

    protected function getStats(): array
    {
        $activeYear = app(CalendarService::class)->getActiveYearForSchool(tenant()?->id ?? '');

        if ($activeYear === null) {
            return [
                Stat::make('Année active', 'Aucune')
                    ->description('Aucune année scolaire active')
                    ->descriptionIcon('heroicon-o-calendar-days')
                    ->color('danger'),
            ];
        }

        $terms = AcademicCalendarTerm::query()
            ->where('calendar_year_id', $activeYear->id)
            ->count();

        $years = AcademicCalendarYear::query()
            ->forSchool($activeYear->school_id)
            ->count();

        return [
            Stat::make('Année active', $activeYear->name)
                ->description("{$activeYear->start_date->format('d/m/Y')} — {$activeYear->end_date->format('d/m/Y')}")
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('success'),
            Stat::make('Périodes', (string) $terms)
                ->description('pour l\'année active')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('primary'),
            Stat::make('Années configurées', (string) $years)
                ->description('pour cet établissement')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('gray'),
        ];
    }
}
