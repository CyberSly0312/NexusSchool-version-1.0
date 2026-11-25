<?php

declare(strict_types=1);

namespace App\Domains\User\Exceptions;

use App\Domains\User\Enums\AccessRevocationFailure;
use RuntimeException;

class AccessRevocationException extends RuntimeException
{
    public function __construct(
        public readonly AccessRevocationFailure $failure,
        ?string $message = null,
    ) {
        parent::__construct($message ?? $failure->message());
    }
}
