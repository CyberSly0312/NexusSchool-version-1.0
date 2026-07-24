<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Widgets;

use App\Domains\School\Models\School;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SetupStatusWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $school = School::find(tenant('id'));
        $settings = $school?->settings ?? [];

        $checks = [
            'École configurée' => filled($school?->name),
            'Années scolaires' => AcademicYear::where('school_id', tenant('id'))->count() > 0,
            'Classes' => AcademicClass::where('school_id', tenant('id'))->count() > 0,
            'Matières' => Subject::where('school_id', tenant('id'))->count() > 0,
            'Enseignants' => User::where('school_id', tenant('id'))
                ->whereHas('roles', fn ($q) => $q->where('name', 'teacher'))
                ->count() > 0,
            'Configuration frais' => isset($settings['fees']),
            'Configuration PDF' => isset($settings['pdf']),
        ];

        $completed = collect($checks)->filter()->count();
        $total = count($checks);
        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

        return [
            Stat::make('Configuration', "{$percentage}%")
                ->description("{$completed}/{$total} étapes complétées")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger'))
                ->chart([$percentage]),
        ];
    }
}
