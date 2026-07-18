<?php

declare(strict_types=1);

namespace App\Domains\Api\Events;

use App\Models\Webhook;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebhookRegistered
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Webhook $webhook,
    ) {
    }
}
