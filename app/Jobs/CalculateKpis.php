<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domains\Analytics\Services\KpiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateKpis implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    public int $timeout = 600;

    public function __construct(
        public readonly string $schoolId,
    ) {
    }

    public function handle(KpiService $kpiService): void
    {
        $count = $kpiService->calculateAllForSchool($this->schoolId);

        Log::info('KPI calculation completed', [
            'school_id' => $this->schoolId,
            'kpis_calculated' => $count,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('KPI calculation failed', [
            'school_id' => $this->schoolId,
            'error' => $exception->getMessage(),
        ]);
    }
}
