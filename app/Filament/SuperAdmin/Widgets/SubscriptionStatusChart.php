<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Subscription;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class SubscriptionStatusChart extends ChartWidget
{
    protected ?string $heading = 'Répartition des abonnements';

    protected static ?int $sort = 12;

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        return Cache::remember('superadmin.subscription_status_chart', 300, function () {
            $data = Subscription::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $labels = ['Actif', 'Expiré', 'Suspendu', 'Annulé'];
            $colors = ['#10b981', '#f59e0b', '#ef4444', '#6b7280'];
            $values = [
                $data['active'] ?? 0,
                $data['expired'] ?? 0,
                $data['suspended'] ?? 0,
                $data['cancelled'] ?? 0,
            ];

            return [
                'datasets' => [
                    [
                        'data' => $values,
                        'backgroundColor' => $colors,
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }
}
