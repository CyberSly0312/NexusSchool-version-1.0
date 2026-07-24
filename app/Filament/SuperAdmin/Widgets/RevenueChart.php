<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Subscription;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenus mensuels (FCFA)';

    protected static ?int $sort = 11;

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = '300s';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        return Cache::remember('superadmin.revenue_chart', 300, function () {
            $data = Subscription::selectRaw('MONTH(created_at) as month, SUM(payment_amount) as total')
                ->whereYear('created_at', now()->year)
                ->where('status', 'active')
                ->groupByRaw('MONTH(created_at)')
                ->pluck('total', 'month')
                ->toArray();

            $labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
            $values = array_map(fn ($m) => $data[$m] ?? 0, range(1, 12));

            return [
                'datasets' => [
                    [
                        'label' => 'Revenus',
                        'data' => $values,
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }
}
