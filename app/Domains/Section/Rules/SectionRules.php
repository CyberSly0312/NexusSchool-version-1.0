<?php

declare(strict_types=1);

namespace App\Domains\Section\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SectionRules
{
    public static function create(): array
    {
        return [
            'grade_level_id' => ['required', 'exists:grade_levels,id'],
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function update(?int $sectionId = null): array
    {
        return [
            'grade_level_id' => ['sometimes', 'required', 'exists:grade_levels,id'],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'code' => ['sometimes', 'required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $sectionId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($sectionId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
