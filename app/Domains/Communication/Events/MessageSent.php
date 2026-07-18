<?php

declare(strict_types=1);

namespace App\Domains\Communication\Events;

use App\Models\Message;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Message $message,
    ) {
    }
}
