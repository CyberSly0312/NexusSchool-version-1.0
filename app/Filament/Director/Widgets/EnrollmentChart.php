<?php

declare(strict_types=1);

namespace App\Filament\Director\Widgets;

use App\Models\StudentEnrollment;
use Filament\Widgets\ChartWidget;

class EnrollmentChart extends ChartWidget
{
    protected ?string $heading = 'Évolution des inscriptions';

    protected static ?int $sort = 10;

    protected ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $tenantId = tenant()?->getKey();

        $data = $tenantId === null
            ? []
            : StudentEnrollment::query()
                ->where('school_id', $tenantId)
                ->whereYear('created_at', now()->year)
                ->get(['created_at'])
                ->countBy(fn (StudentEnrollment $enrollment): int => $enrollment->created_at->month)
                ->all();

        $labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        $values = array_map(fn ($m) => $data[$m] ?? 0, range(1, 12));

        return [
            'datasets' => [
                [
                    'label' => 'Inscriptions',
                    'data' => $values,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
