<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Contracts;

use App\Domains\Attendance\Domain\Contracts\FutureNotificationContract;
use Illuminate\Support\Facades\Log;

/**
 * Audit-only implementation of FutureNotificationContract (SPR-008).
 *
 * The Notifications domain is out of sprint scope, so this adapter records the
 * notification intent in the log and does nothing else. When Notifications
 * lands, this adapter becomes the bridge to the real scheduling pipeline.
 */
final class FutureNotificationAdapter implements FutureNotificationContract
{
    public function schedule(
        string $schoolId,
        string $channel,
        int|string|null $recipientUserId,
        string $subject,
        string $body,
        array $payload = [],
    ): void {
        Log::info('attendance.notification_intent', [
            'school_id' => $schoolId,
            'channel' => $channel,
            'recipient_user_id' => $recipientUserId,
            'subject' => $subject,
            'payload' => $payload,
        ]);
    }
}
