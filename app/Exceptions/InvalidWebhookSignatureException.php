<?php

declare(strict_types=1);

namespace App\Exceptions;

class InvalidWebhookSignatureException extends ApiException
{
    public function __construct()
    {
        parent::__construct('Invalid webhook signature.', 401);
    }
}
