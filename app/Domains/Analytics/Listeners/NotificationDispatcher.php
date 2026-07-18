<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Listeners;

use App\Domains\Analytics\Events\ReportGenerated;
use App\Domains\Analytics\Events\ReportScheduled;
use Illuminate\Support\Facades\Log;

class NotificationDispatcher
{
    public function handleReportGenerated(ReportGenerated $event): void
    {
        Log::info('Analytics notification dispatched for completed report', [
            'report_id' => $event->report->id,
            'report_name' => $event->report->name,
            'school_id' => $event->report->school_id,
            'format' => $event->report->format->value,
        ]);
    }

    public function handleReportScheduled(ReportScheduled $event): void
    {
        Log::info('Analytics notification dispatched for scheduled report', [
            'report_id' => $event->report->id,
            'report_name' => $event->report->name,
            'school_id' => $event->report->school_id,
            'frequency' => $event->report->scheduled_frequency?->value,
        ]);
    }
}
