<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Widgets;

use App\Domains\School\Models\School;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class SchoolsChart extends ChartWidget
{
    protected ?string $heading = 'Écoles par mois';

    protected static ?int $sort = 10;

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        return Cache::remember('superadmin.schools_chart', 300, function () {
            $data = School::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', now()->year)
                ->groupByRaw('MONTH(created_at)')
                ->pluck('count', 'month')
                ->toArray();

            $labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
            $values = array_map(fn ($m) => $data[$m] ?? 0, range(1, 12));

            return [
                'datasets' => [
                    [
                        'label' => 'Écoles créées',
                        'data' => $values,
                        'backgroundColor' => '#3b82f6',
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }
}
