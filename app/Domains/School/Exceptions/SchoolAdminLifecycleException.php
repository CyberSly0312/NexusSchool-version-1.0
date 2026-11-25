<?php

declare(strict_types=1);

namespace App\Domains\School\Exceptions;

use App\Domains\School\Enums\SchoolAdminLifecycleFailure;
use RuntimeException;

class SchoolAdminLifecycleException extends RuntimeException
{
    public function __construct(
        public readonly SchoolAdminLifecycleFailure $failure,
        ?string $message = null,
    ) {
        parent::__construct($message ?? $failure->message());
    }
}
