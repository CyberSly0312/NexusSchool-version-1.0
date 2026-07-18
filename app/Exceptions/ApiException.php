<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ApiException extends Exception
{
    protected int $statusCode;

    protected array $errors;

    public function __construct(string $message, int $statusCode = 400, array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => $this->errors,
        ], $this->statusCode);
    }

    public static function notFound(string $message = 'Resource not found'): static
    {
        return new static($message, 404);
    }

    public static function forbidden(string $message = 'Unauthorized'): static
    {
        return new static($message, 403);
    }

    public static function validation(array $errors): static
    {
        return new static('Validation failed', 422, $errors);
    }
}
