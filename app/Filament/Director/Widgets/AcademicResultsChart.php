<?php

declare(strict_types=1);

namespace App\Filament\Director\Widgets;

use App\Models\AssessmentResult;
use Filament\Widgets\ChartWidget;

class AcademicResultsChart extends ChartWidget
{
    protected ?string $heading = 'Résultats scolaires';

    protected static ?int $sort = 13;

    protected ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $total = AssessmentResult::count();
        if ($total === 0) {
            return [
                'datasets' => [['data' => [1], 'backgroundColor' => ['#e5e7eb']]],
                'labels' => ['Aucune donnée'],
            ];
        }

        $excellent = AssessmentResult::where('average', '>=', 14)->count();
        $good = AssessmentResult::whereBetween('average', [10, 13.99])->count();
        $medium = AssessmentResult::whereBetween('average', [8, 9.99])->count();
        $weak = AssessmentResult::where('average', '<', 8)->count();

        return [
            'datasets' => [
                [
                    'data' => [$excellent, $good, $medium, $weak],
                    'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                ],
            ],
            'labels' => ['Excellent (≥14)', 'Bien (10-13)', 'Moyen (8-9)', 'Faible (<8)'],
        ];
    }
}
