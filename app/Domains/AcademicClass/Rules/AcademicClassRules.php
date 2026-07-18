<?php

declare(strict_types=1);

namespace App\Domains\AcademicClass\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AcademicClassRules
{
    public static function create(): array
    {
        return [
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'grade_level_id' => ['required', 'exists:grade_levels,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
            'display_order' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
            'is_active' => ['sometimes', 'boolean'],
            'homeroom_teacher_id' => ['nullable', 'integer'],
            'classroom' => ['nullable', 'string', 'max:100'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
        ];
    }

    public static function update(?int $academicClassId = null): array
    {
        return [
            'academic_year_id' => ['sometimes', 'required', 'exists:academic_years,id'],
            'program_id' => ['sometimes', 'required', 'exists:programs,id'],
            'grade_level_id' => ['sometimes', 'required', 'exists:grade_levels,id'],
            'section_id' => ['sometimes', 'required', 'exists:sections,id'],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'code' => ['sometimes', 'required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'capacity' => ['sometimes', 'required', 'integer', 'min:1'],
            'display_order' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
            'is_active' => ['sometimes', 'boolean'],
            'homeroom_teacher_id' => ['nullable', 'integer'],
            'classroom' => ['nullable', 'string', 'max:100'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $academicClassId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($academicClassId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
