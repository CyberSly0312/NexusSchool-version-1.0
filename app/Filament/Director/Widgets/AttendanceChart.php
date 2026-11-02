<?php

declare(strict_types=1);

namespace App\Filament\Director\Widgets;

use App\Models\AttendanceRecord;
use Filament\Widgets\ChartWidget;

class AttendanceChart extends ChartWidget
{
    protected ?string $heading = 'Fréquentation';

    protected static ?int $sort = 11;

    protected ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $tenantId = tenant()?->getKey();
        $days = collect(range(0, 6))->map(fn ($d) => now()->startOfWeek()->addDays($d)->toDateString());
        $labels = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        $present = [];
        $absent = [];
        $late = [];

        foreach ($days as $i => $day) {
            $present[$i] = $tenantId === null ? 0 : AttendanceRecord::where('school_id', $tenantId)->where('date', $day)->where('status', 'present')->count();
            $absent[$i] = $tenantId === null ? 0 : AttendanceRecord::where('school_id', $tenantId)->where('date', $day)->where('status', 'absent')->count();
            $late[$i] = $tenantId === null ? 0 : AttendanceRecord::where('school_id', $tenantId)->where('date', $day)->where('status', 'late')->count();
        }

        return [
            'datasets' => [
                ['label' => 'Présents', 'data' => $present, 'backgroundColor' => '#10b981'],
                ['label' => 'Absents', 'data' => $absent, 'backgroundColor' => '#ef4444'],
                ['label' => 'Retards', 'data' => $late, 'backgroundColor' => '#f59e0b'],
            ],
            'labels' => $labels,
        ];
    }
}
