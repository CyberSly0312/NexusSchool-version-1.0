<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domains\Analytics\Services\AnalyticsService;
use App\Domains\Analytics\Services\ReportingService;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateReport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct(
        public readonly string $reportId,
    ) {
    }

    public function handle(ReportingService $reportingService): void
    {
        $report = Report::findOrFail($this->reportId);

        if ($report->status->value !== 'scheduled' && $report->status->value !== 'draft') {
            return;
        }

        $reportingService->markGenerating($report);

        try {
            $config = $report->config ?? [];
            $data = $this->gatherReportData($report, $config);

            $reportingService->markCompleted($report);
        } catch (\Throwable $e) {
            $reportingService->markFailed($report);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Report generation failed', [
            'report_id' => $this->reportId,
            'error' => $exception->getMessage(),
        ]);
    }

    private function gatherReportData(Report $report, array $config): array
    {
        $analyticsService = app(AnalyticsService::class);

        return [
            'report_name' => $report->name,
            'generated_at' => now()->toDateTimeString(),
            'school_id' => $report->school_id,
            'type' => $report->type,
            'format' => $report->format->value,
        ];
    }
}
