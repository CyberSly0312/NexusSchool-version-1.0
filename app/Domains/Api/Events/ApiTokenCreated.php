<?php

declare(strict_types=1);

namespace App\Domains\Api\Events;

use App\Models\ApiToken;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApiTokenCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly ApiToken $token,
    ) {
    }
}
