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

class ConfigStatusWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $school = School::find(tenant('id'));
        $settings = $school?->settings ?? [];
        $tenantId = tenant('id');

        $checks = [
            'Logo configuré' => filled($school?->logo_url),
            'Signature enregistrée' => ! empty($settings['pdf']['pdf_signature_path']),
            'Cachet enregistré' => ! empty($settings['pdf']['pdf_stamp_path']),
            'Année scolaire active' => AcademicYear::where('school_id', $tenantId)->where('status', 'active')->exists(),
            'Classes créées' => AcademicClass::where('school_id', $tenantId)->count() > 0,
            'Enseignants' => User::where('school_id', $tenantId)->whereHas('roles', fn ($q) => $q->where('name', 'teacher'))->count() > 0,
            'Comptes utilisateurs' => User::where('school_id', $tenantId)->count() > 0,
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
