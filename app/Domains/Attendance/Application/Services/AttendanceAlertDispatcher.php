<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Services;

use App\Domains\Attendance\Domain\Contracts\AnalyticsContract;
use App\Domains\Attendance\Domain\Contracts\FutureNotificationContract;
use App\Domains\Attendance\Domain\Events\AttendanceAlertGenerated;
use App\Domains\Attendance\Domain\Events\LowAttendanceDetected;
use App\Domains\Attendance\Infrastructure\Models\AttendanceAlert;
use App\Models\User;

/**
 * Alert engine (SPR-008).
 *
 * Compares computed statistics against the configured thresholds, persists an
 * AttendanceAlert, publishes the domain events and hands the notification
 * intent to FutureNotificationContract. It never sends notifications itself.
 */
class AttendanceAlertDispatcher
{
    /**
     * @param  array<string, mixed>  $metrics
     */
    public function dispatch(
        string $schoolId,
        int|string $enrollmentId,
        array $metrics,
        string $periodStart,
        string $periodEnd,
        int|string|null $sessionId = null,
        ?User $actor = null,
    ): array {
        if (! (bool) config('nexus-attendance.alerts.enabled', true)) {
            return [];
        }

        $alerts = [];
        $thresholds = (array) config('nexus-attendance.alerts', []);

        $rules = [
            [
                'type' => 'low_attendance',
                'severity' => 'high',
                'key' => 'attendance_percentage',
                'threshold' => (float) ($thresholds['attendance_percentage_threshold'] ?? 75.0),
                'below' => true,
            ],
            [
                'type' => 'high_late_percentage',
                'severity' => 'medium',
                'key' => 'late_percentage',
                'threshold' => (float) ($thresholds['late_percentage_threshold'] ?? 20.0),
                'below' => false,
            ],
            [
                'type' => 'consecutive_absences',
                'severity' => 'high',
                'key' => 'consecutive_absences',
                'threshold' => (int) ($thresholds['consecutive_absences_threshold'] ?? 3),
                'below' => false,
            ],
            [
                'type' => 'consecutive_lates',
                'severity' => 'medium',
                'key' => 'consecutive_lates',
                'threshold' => (int) ($thresholds['consecutive_lates_threshold'] ?? 5),
                'below' => false,
            ],
            [
                'type' => 'risk',
                'severity' => 'dynamic',
                'key' => 'risk_score',
                'threshold' => 40.0,
                'below' => false,
            ],
        ];

        foreach ($rules as $rule) {
            $observed = (float) ($metrics[$rule['key']] ?? 0.0);
            $triggered = $rule['below']
                ? $observed < $rule['threshold']
                : $observed >= $rule['threshold'];

            if (! $triggered) {
                continue;
            }

            $severity = $rule['severity'] === 'dynamic'
                ? $this->dynamicSeverity($observed)
                : $rule['severity'];

            $alert = $this->createAlert(
                schoolId: $schoolId,
                enrollmentId: $enrollmentId,
                sessionId: $sessionId,
                type: $rule['type'],
                severity: $severity,
                threshold: (float) $rule['threshold'],
                observedValue: $observed,
                periodStart: $periodStart,
                periodEnd: $periodEnd,
                metrics: $metrics,
                actor: $actor,
            );

            if ($alert === null) {
                continue;
            }

            $alerts[] = $alert;
        }

        return $alerts;
    }

    private function dynamicSeverity(float $riskScore): string
    {
        $levels = (array) config('nexus-attendance.risk.levels', []);

        foreach (['high', 'medium', 'low'] as $level) {
            if (isset($levels[$level]) && $riskScore >= (float) $levels[$level]['min']) {
                return $level;
            }
        }

        return 'low';
    }

    /**
     * @param  array<string, mixed>  $metrics
     */
    private function createAlert(
        string $schoolId,
        int|string $enrollmentId,
        int|string|null $sessionId,
        string $type,
        string $severity,
        float $threshold,
        float $observedValue,
        string $periodStart,
        string $periodEnd,
        array $metrics,
        ?User $actor = null,
    ): ?AttendanceAlert {
        // An already-open alert of the same type for the same enrollment
        // suppresses new ones until it is resolved, so repeated summary runs
        // never pile up duplicate alerts.
        $alreadyOpen = AttendanceAlert::query()
            ->forSchool($schoolId)
            ->where('enrollment_id', $enrollmentId)
            ->where('alert_type', $type)
            ->where('status', 'open')
            ->exists();

        if ($alreadyOpen) {
            return null;
        }

        $alert = AttendanceAlert::create([
            'school_id' => $schoolId,
            'enrollment_id' => $enrollmentId,
            'attendance_session_id' => $sessionId,
            'alert_type' => $type,
            'severity' => $severity,
            'threshold' => $threshold,
            'observed_value' => $observedValue,
            'payload' => array_merge($metrics, [
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ]),
            'detected_at' => now(),
            'status' => 'open',
        ]);

        AttendanceAlertGenerated::dispatch($alert, $actor);

        if ($type === 'low_attendance') {
            LowAttendanceDetected::dispatch(
                alert: $alert,
                enrollmentId: $enrollmentId,
                attendancePercentage: $observedValue,
                threshold: $threshold,
                actor: $actor,
            );
        }

        $this->futureNotification->schedule(
            schoolId: $schoolId,
            channel: 'guardian',
            recipientUserId: null,
            subject: "Alerte présence — {$type}",
            body: "Alerte présence [{$type}] détectée (valeur {$observedValue}).",
            payload: $alert->toArray(),
        );

        $this->analytics->recordMetric($schoolId, "attendance.alert.{$type}", $observedValue, $alert->toArray());

        return $alert;
    }

    public function __construct(
        private readonly FutureNotificationContract $futureNotification,
        private readonly AnalyticsContract $analytics,
    ) {
    }
}
