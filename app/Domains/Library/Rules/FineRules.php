<?php

declare(strict_types=1);

namespace App\Domains\Library\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FineRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'book_loan_id' => ['nullable', 'integer', 'exists:book_loans,id'],
            'borrower_id' => ['required', 'integer'],
            'borrower_type' => ['sometimes', 'string', 'in:student,teacher'],
            'reason' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'fine_date' => ['nullable', 'date'],
            'status' => ['sometimes', 'string', 'in:pending,partially_paid,paid,waived'],
            'processed_by' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $fineId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($fineId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public static function update(?int $fineId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'book_loan_id' => ['nullable', 'integer', 'exists:book_loans,id'],
            'borrower_id' => ['sometimes', 'required', 'integer'],
            'borrower_type' => ['sometimes', 'string', 'in:student,teacher'],
            'reason' => ['sometimes', 'required', 'string', 'max:255'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'fine_date' => ['nullable', 'date'],
            'processed_by' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
