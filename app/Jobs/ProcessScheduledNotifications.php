<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domains\Communication\Enums\NotificationStatus;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessScheduledNotifications implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 120;

    public function handle(): void
    {
        $scheduled = Notification::where('status', NotificationStatus::Queued)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($scheduled as $notification) {
            SendNotification::dispatch($notification->id);
        }
    }
}
