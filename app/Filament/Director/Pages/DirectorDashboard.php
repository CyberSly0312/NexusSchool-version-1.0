<?php

declare(strict_types=1);

namespace App\Filament\Director\Pages;

use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;

class DirectorDashboard extends Dashboard
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Briefcase;
    protected static ?string $navigationLabel = 'Tableau de bord';
    protected static ?string $title = 'Pilotage de l\'établissement';
    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [
            \App\Filament\Director\Widgets\DirectorStatsOverview::class,
            \App\Filament\Director\Widgets\EnrollmentChart::class,
            \App\Filament\Director\Widgets\AttendanceChart::class,
            \App\Filament\Director\Widgets\PaymentsChart::class,
            \App\Filament\Director\Widgets\AcademicResultsChart::class,
        ];
    }
}
