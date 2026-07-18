<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Listeners;

use App\Domains\Analytics\Events\ReportGenerated;
use Illuminate\Support\Facades\Log;

class ExportGenerator
{
    public function handleReportGenerated(ReportGenerated $event): void
    {
        Log::info('Analytics export generated for report', [
            'report_id' => $event->report->id,
            'report_name' => $event->report->name,
            'school_id' => $event->report->school_id,
            'format' => $event->report->format->value,
            'generated_at' => $event->report->last_generated_at,
        ]);
    }
}
