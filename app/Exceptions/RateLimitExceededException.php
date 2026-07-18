<?php

declare(strict_types=1);

namespace App\Exceptions;

class RateLimitExceededException extends ApiException
{
    public function __construct(int $retryAfter = 60)
    {
        parent::__construct('Rate limit exceeded.', 429, ['retry_after' => $retryAfter]);
    }
}
