<?php

declare(strict_types=1);

namespace App\Domains\Communication\Events;

use App\Models\Notification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationDelivered
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Notification $notification,
    ) {
    }
}
