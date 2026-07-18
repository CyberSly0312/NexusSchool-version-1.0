<?php

declare(strict_types=1);

namespace App\Domains\Subject\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SubjectRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'subject_category_id' => ['required', 'integer', 'exists:subject_categories,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50'],
            'short_name' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'display_order' => ['sometimes', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_elective' => ['sometimes', 'boolean'],
            'is_core_subject' => ['sometimes', 'boolean'],
            'weekly_hours' => ['required', 'integer', 'min:1'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function update(?int $subjectId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'subject_category_id' => ['sometimes', 'required', 'integer', 'exists:subject_categories,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'code' => ['sometimes', 'required', 'string', 'max:50'],
            'short_name' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'display_order' => ['sometimes', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_elective' => ['sometimes', 'boolean'],
            'is_core_subject' => ['sometimes', 'boolean'],
            'weekly_hours' => ['sometimes', 'required', 'integer', 'min:1'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $subjectId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($subjectId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
