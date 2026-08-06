<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Contracts;

use App\Domains\Attendance\Domain\Contracts\AnalyticsContract;
use Illuminate\Support\Facades\Log;

/**
 * Audit-only implementation of AnalyticsContract (SPR-008).
 *
 * The Analytics dashboard is out of sprint scope. This adapter records the
 * metric in the log so downstream wiring can be verified without a live
 * pipeline. Attendance statistics remain fully computed inside this domain.
 */
final class AnalyticsAdapter implements AnalyticsContract
{
    public function recordMetric(string $schoolId, string $metric, float $value, array $payload = []): void
    {
        Log::info('attendance.metric', [
            'school_id' => $schoolId,
            'metric' => $metric,
            'value' => $value,
            'payload' => $payload,
        ]);
    }
}
