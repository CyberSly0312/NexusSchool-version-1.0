<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domains\Analytics\Services\AnalyticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateAnalyticsSnapshot implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    public int $timeout = 300;

    public function __construct(
        public readonly string $schoolId,
        public readonly string $snapshotType,
    ) {
    }

    public function handle(AnalyticsService $analyticsService): void
    {
        $data = match ($this->snapshotType) {
            'enrollment' => $analyticsService->getEnrollmentStats($this->schoolId),
            'attendance' => $analyticsService->getAttendanceStats($this->schoolId),
            'library' => $analyticsService->getLibraryStats($this->schoolId),
            'transport' => $analyticsService->getTransportStats($this->schoolId),
            'communication' => $analyticsService->getCommunicationStats($this->schoolId),
            default => [],
        };

        $analyticsService->createSnapshot(
            schoolId: $this->schoolId,
            type: $this->snapshotType,
            data: $data,
            periodStart: now()->startOfDay()->toDateTimeString(),
            periodEnd: now()->endOfDay()->toDateTimeString(),
        );

        Log::info('Analytics snapshot created', [
            'school_id' => $this->schoolId,
            'snapshot_type' => $this->snapshotType,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Analytics snapshot creation failed', [
            'school_id' => $this->schoolId,
            'snapshot_type' => $this->snapshotType,
            'error' => $exception->getMessage(),
        ]);
    }
}
