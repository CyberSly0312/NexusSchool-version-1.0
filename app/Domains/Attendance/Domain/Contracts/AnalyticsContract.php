<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Contracts;

/**
 * Gateway to the Analytics domain (SPR-008).
 *
 * Attendance statistics are computed inside this domain; pushing them into a
 * cross-domain analytics pipeline is owned by the future Analytics domain.
 * During this sprint the implementation is an audit-only stub.
 */
interface AnalyticsContract
{
    /**
     * Records a computed attendance metric for downstream analytics.
     *
     * @param array<string, mixed> $payload
     */
    public function recordMetric(string $schoolId, string $metric, float $value, array $payload = []): void;
}
