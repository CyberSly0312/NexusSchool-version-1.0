<?php

declare(strict_types=1);

namespace App\Domains\Ai\Events;

use App\Models\AiMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageGenerated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AiMessage $message,
    ) {
    }
}
