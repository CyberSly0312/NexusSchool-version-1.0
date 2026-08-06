<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Contracts;

/**
 * Gateway to the future Notifications domain (SPR-008).
 *
 * The alert engine never sends notifications: it only publishes domain events.
 * When the Notifications domain lands, this contract becomes the bridge that
 * turns AttendanceAlertGenerated / LowAttendanceDetected into scheduled
 * notifications. During this sprint the implementation is intentionally a
 * no-op audit stub so the engine never depends on a concrete notifier.
 */
interface FutureNotificationContract
{
    /**
     * Registers an intent to notify an actor about an attendance alert.
     *
     * @param array<string, mixed> $payload
     */
    public function schedule(
        string $schoolId,
        string $channel,
        int|string|null $recipientUserId,
        string $subject,
        string $body,
        array $payload = [],
    ): void;
}
