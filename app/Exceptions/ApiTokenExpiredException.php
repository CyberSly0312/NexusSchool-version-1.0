<?php

declare(strict_types=1);

namespace App\Exceptions;

class ApiTokenExpiredException extends ApiException
{
    public function __construct()
    {
        parent::__construct('API token has expired.', 401);
    }
}
