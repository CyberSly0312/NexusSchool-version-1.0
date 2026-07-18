<?php

declare(strict_types=1);

namespace App\Domains\Api\Events;

use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebhookFailed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Webhook $webhook,
        public readonly WebhookDelivery $delivery,
    ) {
    }
}
