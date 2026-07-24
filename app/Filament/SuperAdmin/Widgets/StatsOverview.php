<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Widgets;

use App\Domains\School\Models\School;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $data = Cache::remember('superadmin.stats_overview', 30, function () {
            $totalSchools = School::count();
            $activeSchools = School::where('status', 'active')->count();
            $totalUsers = User::count();
            $totalStudents = \DB::table('students')->count();
            $activeSubscriptions = Subscription::where('status', 'active')->count();
            $totalPlans = Plan::where('is_active', true)->count();

            $monthlyRevenue = Subscription::where('status', 'active')
                ->whereHas('plan', fn ($q) => $q->where('billing_period', 'monthly'))
                ->sum('payment_amount');

            $yearlyRevenue = Subscription::where('status', 'active')
                ->whereHas('plan', fn ($q) => $q->where('billing_period', 'yearly'))
                ->sum('payment_amount');

            return compact(
                'totalSchools', 'activeSchools', 'totalUsers', 'totalStudents',
                'activeSubscriptions', 'totalPlans', 'monthlyRevenue', 'yearlyRevenue'
            );
        });

        return [
            Stat::make('Total écoles', $data['totalSchools'])
                ->description('Écoles enregistrées')
                ->descriptionIcon('heroicon-o-building-library')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),
            Stat::make('Écoles actives', $data['activeSchools'])
                ->description($data['totalSchools'] > 0 ? round(($data['activeSchools'] / $data['totalSchools']) * 100) . '% du total' : 'Aucune')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Utilisateurs', number_format($data['totalUsers']))
                ->description('Tous rôles confondus')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),
            Stat::make('Élèves', number_format($data['totalStudents']))
                ->description('Inscrits dans les écoles')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('info'),
            Stat::make('Abonnements actifs', $data['activeSubscriptions'])
                ->description("{$data['totalPlans']} formule(s) disponible(s)")
                ->descriptionIcon('heroicon-o-receipt-percent')
                ->color('warning'),
            Stat::make('Revenu mensuel', number_format($data['monthlyRevenue'], 0, ',', ' ') . ' FCFA')
                ->description('Abonnements mensuels')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('Revenu annuel', number_format($data['yearlyRevenue'], 0, ',', ' ') . ' FCFA')
                ->description('Abonnements annuels')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('info'),
        ];
    }
}
