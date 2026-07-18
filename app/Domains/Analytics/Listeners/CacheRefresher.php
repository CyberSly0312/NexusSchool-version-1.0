<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Listeners;

use App\Domains\Analytics\Events\KpiRecalculated;
use Illuminate\Support\Facades\Log;

class CacheRefresher
{
    public function handleKpiRecalculated(KpiRecalculated $event): void
    {
        Log::info('Analytics cache refreshed for KPI', [
            'kpi_id' => $event->kpi->id,
            'kpi_name' => $event->kpi->name,
            'school_id' => $event->kpi->school_id,
            'cached_value' => $event->kpi->cached_value,
        ]);
    }
}
