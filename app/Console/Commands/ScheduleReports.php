<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Analytics\Services\ReportingService;
use App\Jobs\GenerateReport;
use Illuminate\Console\Command;

class ScheduleReports extends Command
{
    protected $signature = 'analytics:schedule-reports';

    protected $description = 'Process and dispatch scheduled reports';

    public function handle(ReportingService $reportingService): int
    {
        $reports = $reportingService->getScheduledReports();

        if ($reports->isEmpty()) {
            $this->info('No scheduled reports to process.');

            return self::SUCCESS;
        }

        foreach ($reports as $report) {
            GenerateReport::dispatch($report->id);
            $this->info("Dispatched report generation: {$report->name}");
        }

        $this->info("Processed {$reports->count()} scheduled report(s).");

        return self::SUCCESS;
    }
}
