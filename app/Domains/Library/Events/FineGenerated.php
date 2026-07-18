<?php

declare(strict_types=1);

namespace App\Domains\Library\Events;

use App\Models\Fine;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FineGenerated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Fine $fine,
    ) {
    }
}
