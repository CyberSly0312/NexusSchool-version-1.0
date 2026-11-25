<?php

declare(strict_types=1);

namespace App\Domains\AccountRecovery\Exceptions;

use App\Domains\AccountRecovery\Enums\PasswordResetFailure;
use RuntimeException;

class PasswordResetException extends RuntimeException
{
    /**
     * @param  list<string>  $validationMessages
     */
    public function __construct(
        public readonly PasswordResetFailure $failure,
        ?string $message = null,
        public readonly array $validationMessages = [],
    ) {
        parent::__construct($message ?? $failure->message());
    }
}
