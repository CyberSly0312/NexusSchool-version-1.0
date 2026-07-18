<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Listeners;

use App\Domains\Analytics\Events\DashboardCreated;
use App\Domains\Analytics\Events\KpiRecalculated;
use App\Domains\Analytics\Events\ReportGenerated;
use App\Domains\Analytics\Events\SnapshotCreated;
use App\Domains\Analytics\Events\WidgetAdded;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    public function handleDashboardCreated(DashboardCreated $event): void
    {
        Log::info('Audit: Dashboard created', [
            'dashboard_id' => $event->dashboard->id,
            'dashboard_name' => $event->dashboard->name,
            'school_id' => $event->dashboard->school_id,
            'type' => $event->dashboard->type->value,
        ]);
    }

    public function handleWidgetAdded(WidgetAdded $event): void
    {
        Log::info('Audit: Widget added to dashboard', [
            'widget_id' => $event->widget->id,
            'widget_title' => $event->widget->title,
            'dashboard_id' => $event->widget->dashboard_id,
            'school_id' => $event->widget->school_id,
        ]);
    }

    public function handleReportGenerated(ReportGenerated $event): void
    {
        Log::info('Audit: Report generated', [
            'report_id' => $event->report->id,
            'report_name' => $event->report->name,
            'school_id' => $event->report->school_id,
            'format' => $event->report->format->value,
        ]);
    }

    public function handleSnapshotCreated(SnapshotCreated $event): void
    {
        Log::info('Audit: Analytics snapshot created', [
            'snapshot_id' => $event->snapshot->id,
            'snapshot_type' => $event->snapshot->snapshot_type,
            'school_id' => $event->snapshot->school_id,
        ]);
    }

    public function handleKpiRecalculated(KpiRecalculated $event): void
    {
        Log::info('Audit: KPI recalculated', [
            'kpi_id' => $event->kpi->id,
            'kpi_name' => $event->kpi->name,
            'school_id' => $event->kpi->school_id,
            'cached_value' => $event->kpi->cached_value,
        ]);
    }
}
