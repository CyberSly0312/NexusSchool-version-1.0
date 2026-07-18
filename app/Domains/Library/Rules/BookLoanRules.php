<?php

declare(strict_types=1);

namespace App\Domains\Library\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BookLoanRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'book_copy_id' => ['required', 'integer', 'exists:book_copies,id'],
            'borrower_id' => ['required', 'integer'],
            'borrower_type' => ['sometimes', 'string', 'in:student,teacher'],
            'loan_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after:today'],
            'issued_by' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $loanId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($loanId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public static function update(?int $loanId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'book_copy_id' => ['sometimes', 'required', 'integer', 'exists:book_copies,id'],
            'borrower_id' => ['sometimes', 'required', 'integer'],
            'borrower_type' => ['sometimes', 'string', 'in:student,teacher'],
            'loan_date' => ['nullable', 'date'],
            'due_date' => ['sometimes', 'date'],
            'returned_to' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
