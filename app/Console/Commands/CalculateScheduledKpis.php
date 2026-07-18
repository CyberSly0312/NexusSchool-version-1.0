<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CalculateKpis;
use App\Models\Kpi;
use Illuminate\Console\Command;

class CalculateScheduledKpis extends Command
{
    protected $signature = 'analytics:calculate-kpis';

    protected $description = 'Calculate KPIs that are due for recalculation';

    public function handle(): int
    {
        $kpis = Kpi::where('status', 'active')
            ->whereNotNull('calculation_frequency')
            ->where(function ($query) {
                $query->whereNull('last_calculated_at')
                    ->orWhere('last_calculated_at', '<', now()->subHour());
            })
            ->get();

        if ($kpis->isEmpty()) {
            $this->info('No KPIs due for calculation.');

            return self::SUCCESS;
        }

        $schoolIds = $kpis->pluck('school_id')->unique();

        foreach ($schoolIds as $schoolId) {
            CalculateKpis::dispatch($schoolId);
            $this->info("Dispatched KPI calculation for school: {$schoolId}");
        }

        $this->info("Processed KPI calculations for {$schoolIds->count()} school(s).");

        return self::SUCCESS;
    }
}
