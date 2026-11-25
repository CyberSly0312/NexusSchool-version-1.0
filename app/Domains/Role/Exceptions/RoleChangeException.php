<?php

declare(strict_types=1);

namespace App\Domains\Role\Exceptions;

use RuntimeException;
use Throwable;

final class RoleChangeException extends RuntimeException
{
    public const REASON_ACTOR = 'actor';
    public const REASON_TARGET = 'target';
    public const REASON_SCHOOL = 'school';
    public const REASON_ROLE = 'role';
    public const REASON_INVARIANT = 'invariant';

    public function __construct(
        string $message,
        public readonly string $reason = self::REASON_ACTOR,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
