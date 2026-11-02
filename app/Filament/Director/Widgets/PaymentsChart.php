<?php

declare(strict_types=1);

namespace App\Filament\Director\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class PaymentsChart extends ChartWidget
{
    protected ?string $heading = 'Évolution des paiements';

    protected static ?int $sort = 12;

    protected ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $tenantId = tenant()?->getKey();

        $data = $tenantId === null
            ? []
            : Payment::where('school_id', $tenantId)
                ->where('status', 'paid')
                ->selectRaw('MONTH(paid_at) as month, SUM(amount) as total')
                ->whereYear('paid_at', now()->year)
                ->groupByRaw('MONTH(paid_at)')
                ->pluck('total', 'month')
                ->toArray();

        $labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        $values = array_map(fn ($m) => round($data[$m] ?? 0), range(1, 12));

        return [
            'datasets' => [
                [
                    'label' => 'Montant (FCFA)',
                    'data' => $values,
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
