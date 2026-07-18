<?php

declare(strict_types=1);

namespace App\Domains\Library\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReservationRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'borrower_id' => ['required', 'integer'],
            'borrower_type' => ['sometimes', 'string', 'in:student,teacher'],
            'reservation_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $reservationId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($reservationId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public static function update(?int $reservationId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'book_id' => ['sometimes', 'required', 'integer', 'exists:books,id'],
            'borrower_id' => ['sometimes', 'required', 'integer'],
            'borrower_type' => ['sometimes', 'string', 'in:student,teacher'],
            'reservation_date' => ['nullable', 'date'],
            'expiry_date' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
