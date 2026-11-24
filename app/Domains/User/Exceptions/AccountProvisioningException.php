<?php

declare(strict_types=1);

namespace App\Domains\User\Exceptions;

use RuntimeException;
use Throwable;

final class AccountProvisioningException extends RuntimeException
{
    public const REASON_GENERIC = 'generic';
    public const REASON_ACTOR = 'actor';
    public const REASON_SCHOOL = 'school';
    public const REASON_EMAIL = 'email';
    public const REASON_ROLE = 'role';

    public function __construct(
        string $message,
        public readonly string $reason = self::REASON_GENERIC,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
