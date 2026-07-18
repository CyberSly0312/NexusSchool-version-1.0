<?php

declare(strict_types=1);

namespace App\Domains\Library\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BookRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'isbn' => ['nullable', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'language' => ['sometimes', 'string', 'max:50'],
            'edition' => ['nullable', 'string', 'max:50'],
            'publication_year' => ['nullable', 'integer', 'min:1000', 'max:'.(date('Y') + 1)],
            'publisher_id' => ['nullable', 'integer', 'exists:publishers,id'],
            'book_category_id' => ['nullable', 'integer', 'exists:book_categories,id'],
            'shelf_location' => ['nullable', 'string', 'max:100'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'author_ids' => ['nullable', 'array'],
            'author_ids.*' => ['integer', 'exists:authors,id'],
            'status' => ['sometimes', 'string', 'in:active,inactive,archived'],
        ];
    }

    public static function update(?int $bookId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'isbn' => ['nullable', 'string', 'max:20'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'language' => ['sometimes', 'string', 'max:50'],
            'edition' => ['nullable', 'string', 'max:50'],
            'publication_year' => ['nullable', 'integer', 'min:1000', 'max:'.(date('Y') + 1)],
            'publisher_id' => ['nullable', 'integer', 'exists:publishers,id'],
            'book_category_id' => ['nullable', 'integer', 'exists:book_categories,id'],
            'shelf_location' => ['nullable', 'string', 'max:100'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'author_ids' => ['nullable', 'array'],
            'author_ids.*' => ['integer', 'exists:authors,id'],
            'status' => ['sometimes', 'string', 'in:active,inactive,archived'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $bookId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($bookId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
