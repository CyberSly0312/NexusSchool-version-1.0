<?php

declare(strict_types=1);

namespace App\Filament\Director\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

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
            : Payment::query()
                ->where('school_id', $tenantId)
                ->where('status', 'paid')
                ->whereYear('paid_at', now()->year)
                ->get(['paid_at', 'amount'])
                ->groupBy(fn (Payment $payment): int => (int) Carbon::parse($payment->paid_at)->month)
                ->map(fn ($payments): float => (float) $payments->sum(fn (Payment $payment): float => (float) $payment->amount))
                ->all();

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
