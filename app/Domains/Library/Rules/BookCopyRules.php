<?php

declare(strict_types=1);

namespace App\Domains\Library\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BookCopyRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'barcode' => ['required', 'string', 'max:50'],
            'inventory_number' => ['required', 'string', 'max:50'],
            'acquisition_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'condition' => ['sometimes', 'string', 'in:new,good,fair,poor,damaged'],
            'status' => ['sometimes', 'string', 'in:available,reserved,borrowed,returned,archived'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function update(?int $copyId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'book_id' => ['sometimes', 'required', 'integer', 'exists:books,id'],
            'barcode' => ['sometimes', 'required', 'string', 'max:50'],
            'inventory_number' => ['sometimes', 'required', 'string', 'max:50'],
            'acquisition_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'condition' => ['sometimes', 'string', 'in:new,good,fair,poor,damaged'],
            'status' => ['sometimes', 'string', 'in:available,reserved,borrowed,returned,archived'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $copyId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($copyId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
