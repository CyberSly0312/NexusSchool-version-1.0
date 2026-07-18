<?php

declare(strict_types=1);

namespace App\Domains\Communication\Services;

use App\Domains\Communication\DTOs\NotificationData;
use App\Domains\Communication\Enums\DeliveryStatus;
use App\Domains\Communication\Enums\NotificationStatus;
use App\Domains\Communication\Events\NotificationDelivered;
use App\Domains\Communication\Events\NotificationFailed;
use App\Domains\Communication\Events\NotificationQueued;
use App\Domains\Communication\Events\NotificationSent;
use App\Domains\Communication\Rules\NotificationRules;
use App\Models\Notification;
use App\Models\NotificationDelivery;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function __construct(
        protected NotificationRules $notificationRules,
    ) {
    }

    public function create(mixed $data): NotificationData
    {
        $validated = $this->notificationRules->validate($data, 'create');

        $recipientIds = $validated['recipient_ids'];
        unset($validated['recipient_ids']);

        $validated['status'] = $validated['status'] ?? NotificationStatus::Pending->value;
        $validated['channel'] = $validated['channel'] ?? 'in_app';

        $notification = DB::transaction(function () use ($validated, $recipientIds) {
            $notification = Notification::create($validated);

            foreach ($recipientIds as $recipientId) {
                NotificationDelivery::create([
                    'school_id' => $notification->school_id,
                    'notification_id' => $notification->id,
                    'recipient_id' => $recipientId,
                    'channel' => $notification->channel,
                    'status' => DeliveryStatus::Pending->value,
                ]);
            }

            return $notification;
        });

        return NotificationData::create($notification->toArray());
    }

    public function queue(string $id): NotificationData
    {
        $notification = Notification::findOrFail($id);

        if (! $notification->status->canTransitionTo(NotificationStatus::Queued)) {
            throw new \InvalidArgumentException(
                "Cannot transition from '{$notification->status->value}' to 'queued'."
            );
        }

        $notification->update(['status' => NotificationStatus::Queued->value]);

        NotificationQueued::dispatch($notification);

        return NotificationData::create($notification->fresh()->toArray());
    }

    public function send(string $id): NotificationData
    {
        $notification = Notification::findOrFail($id);

        if (! $notification->status->canTransitionTo(NotificationStatus::Sent)) {
            throw new \InvalidArgumentException(
                "Cannot transition from '{$notification->status->value}' to 'sent'."
            );
        }

        DB::transaction(function () use ($notification) {
            $notification->update([
                'status' => NotificationStatus::Sent->value,
                'sent_at' => now(),
            ]);

            $notification->deliveries()->update([
                'status' => DeliveryStatus::Sent->value,
                'sent_at' => now(),
            ]);
        });

        NotificationSent::dispatch($notification);

        return NotificationData::create($notification->fresh()->toArray());
    }

    public function markDelivered(string $id): NotificationData
    {
        $notification = Notification::findOrFail($id);

        DB::transaction(function () use ($notification) {
            $notification->update([
                'status' => NotificationStatus::Delivered->value,
                'delivered_at' => now(),
            ]);

            $notification->deliveries()->update([
                'status' => DeliveryStatus::Delivered->value,
                'delivered_at' => now(),
            ]);
        });

        NotificationDelivered::dispatch($notification);

        return NotificationData::create($notification->fresh()->toArray());
    }

    public function markFailed(string $id, ?string $error = null): NotificationData
    {
        $notification = Notification::findOrFail($id);

        DB::transaction(function () use ($notification, $error) {
            $notification->update([
                'status' => NotificationStatus::Failed->value,
                'last_error' => $error,
                'retry_count' => $notification->retry_count + 1,
            ]);

            $notification->deliveries()->update([
                'status' => DeliveryStatus::Failed->value,
                'failed_at' => now(),
                'error_message' => $error,
            ]);
        });

        NotificationFailed::dispatch($notification);

        return NotificationData::create($notification->fresh()->toArray());
    }

    public function cancel(string $id): NotificationData
    {
        $notification = Notification::findOrFail($id);

        if (! $notification->status->canTransitionTo(NotificationStatus::Cancelled)) {
            throw new \InvalidArgumentException(
                "Cannot transition from '{$notification->status->value}' to 'cancelled'."
            );
        }

        $notification->update(['status' => NotificationStatus::Cancelled->value]);

        return NotificationData::create($notification->fresh()->toArray());
    }

    public function retry(string $id): NotificationData
    {
        $notification = Notification::findOrFail($id);

        if (! $notification->canRetry()) {
            throw new \InvalidArgumentException('Notification cannot be retried.');
        }

        return $this->queue($id);
    }

    public function schedule(string $id, string $scheduledAt): NotificationData
    {
        $notification = Notification::findOrFail($id);

        $notification->update([
            'scheduled_at' => $scheduledAt,
            'status' => NotificationStatus::Queued->value,
        ]);

        NotificationQueued::dispatch($notification);

        return NotificationData::create($notification->fresh()->toArray());
    }

    public function markRead(string $id): void
    {
        $notification = Notification::findOrFail($id);

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }
    }

    public function getForUser(string $userId, ?string $schoolId = null): Collection
    {
        $query = Notification::where('deliveries.recipient_id', $userId)
            ->leftJoin('notification_deliveries', 'notifications.id', '=', 'notification_deliveries.notification_id')
            ->select('notifications.*')
            ->distinct();

        if ($schoolId) {
            $query->where('notifications.school_id', $schoolId);
        }

        return $query->latest()->get();
    }

    public function getUnreadCount(string $userId): int
    {
        return Notification::where('deliveries.recipient_id', $userId)
            ->whereNull('notifications.read_at')
            ->leftJoin('notification_deliveries', 'notifications.id', '=', 'notification_deliveries.notification_id')
            ->count();
    }

    public function getPendingScheduled(): Collection
    {
        return Notification::where('status', NotificationStatus::Queued)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();
    }
}
