<?php

declare(strict_types=1);

namespace App\Domains\Library\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BookCategoryRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:book_categories,id'],
            'status' => ['sometimes', 'string', 'in:active,inactive,archived'],
        ];
    }

    public static function update(?int $categoryId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:book_categories,id'],
            'status' => ['sometimes', 'string', 'in:active,inactive,archived'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $categoryId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($categoryId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
