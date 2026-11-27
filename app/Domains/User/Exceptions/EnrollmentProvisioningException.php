<?php

declare(strict_types=1);

namespace App\Domains\User\Exceptions;

use App\Domains\User\Enums\EnrollmentProvisioningRejectionCode;
use RuntimeException;

final class EnrollmentProvisioningException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly EnrollmentProvisioningRejectionCode $reasonCode,
    ) {
        parent::__construct($message);
    }

    public static function for(
        EnrollmentProvisioningRejectionCode $reasonCode,
        string $message,
    ): self {
        return new self($message, $reasonCode);
    }
}
