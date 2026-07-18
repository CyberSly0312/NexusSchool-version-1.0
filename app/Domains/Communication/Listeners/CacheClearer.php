<?php

declare(strict_types=1);

namespace App\Domains\Communication\Listeners;

use App\Domains\Communication\Events\AnnouncementPublished;
use App\Domains\Communication\Events\NotificationSent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class CacheClearer
{
    use Dispatchable;
    use InteractsWithQueue;

    public function handleAnnouncementPublished(AnnouncementPublished $event): void
    {
        $schoolId = $event->announcement->school_id;

        Cache::forget("announcements_active_{$schoolId}");
        Cache::forget("announcements_pinned_{$schoolId}");
    }

    public function handleNotificationSent(NotificationSent $event): void
    {
        $notification = $event->notification;

        if ($notification->sender_id) {
            Cache::forget("user_unread_notifications_{$notification->sender_id}");
        }
    }
}
