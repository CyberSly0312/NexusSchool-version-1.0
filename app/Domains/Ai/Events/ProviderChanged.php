<?php

declare(strict_types=1);

namespace App\Domains\Ai\Events;

use App\Models\AiProvider;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AiProvider $provider,
        public readonly ?string $previousDriver,
    ) {
    }
}
