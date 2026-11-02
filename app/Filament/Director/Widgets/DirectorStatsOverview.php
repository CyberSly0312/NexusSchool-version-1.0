<?php

declare(strict_types=1);

namespace App\Filament\Director\Widgets;

use App\Models\AttendanceRecord;
use App\Models\GeneratedDocument;
use App\Models\Incident;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DirectorStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $tenantId = tenant()?->getKey();

        if ($tenantId === null) {
            return [];
        }

        $totalStudents = Student::where('school_id', $tenantId)->count();
        $totalTeachers = User::where('school_id', $tenantId)
            ->whereHas('roles', fn ($q) => $q->where('name', 'teacher'))
            ->count();
        $totalStaff = User::where('school_id', $tenantId)->count();

        $today = now()->toDateString();
        $presentToday = AttendanceRecord::where('school_id', $tenantId)->where('date', $today)->where('status', 'present')->count();
        $absentToday = AttendanceRecord::where('school_id', $tenantId)->where('date', $today)->where('status', 'absent')->count();
        $lateToday = AttendanceRecord::where('school_id', $tenantId)->where('date', $today)->where('status', 'late')->count();

        $pendingPayments = Payment::where('school_id', $tenantId)->where('status', 'pending')->count();
        $todayPayments = Payment::where('school_id', $tenantId)->where('paid_at', $today)->sum('amount');

        $pendingDocuments = GeneratedDocument::where('school_id', $tenantId)->whereNull('signed_at')->count();
        $openIncidents = Incident::where('school_id', $tenantId)->where('status', 'open')->count();

        return [
            Stat::make('Total élèves', $totalStudents)
                ->description('Inscrits')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary')
                ->chart([3, 5, 4, 6, 7, 5, 8]),
            Stat::make('Enseignants', $totalTeachers)
                ->description("Sur $totalStaff agents")
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('info'),
            Stat::make('Présents aujourd\'hui', $presentToday)
                ->description("$absentToday absent(s), $lateToday en retard")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Paiements du jour', number_format((float) $todayPayments, 0, ',', ' ') . ' FCFA')
                ->description("$pendingPayments en attente")
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('warning'),
            Stat::make('Documents à valider', $pendingDocuments)
                ->description('En attente de signature')
                ->descriptionIcon('heroicon-o-document-check')
                ->color('info'),
            Stat::make('Incidents ouverts', $openIncidents)
                ->description('Nécessitent une action')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($openIncidents > 0 ? 'danger' : 'success'),
            Stat::make('Absences aujourd\'hui', $absentToday)
                ->description('Élèves absents')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color($absentToday > 0 ? 'warning' : 'success'),
        ];
    }
}
