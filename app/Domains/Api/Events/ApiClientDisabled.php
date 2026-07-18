<?php

declare(strict_types=1);

namespace App\Domains\Api\Events;

use App\Models\ApiClient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApiClientDisabled
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly ApiClient $client,
    ) {
    }
}
