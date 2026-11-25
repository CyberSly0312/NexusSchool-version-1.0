<?php

declare(strict_types=1);

namespace App\Domains\User\Exceptions;

use App\Domains\User\Enums\PasswordChangeFailure;
use RuntimeException;

class PasswordChangeException extends RuntimeException
{
    /**
     * @param  list<string>  $validationMessages
     */
    public function __construct(
        public readonly PasswordChangeFailure $failure,
        ?string $message = null,
        public readonly array $validationMessages = [],
    ) {
        parent::__construct($message ?? $failure->message());
    }
}
