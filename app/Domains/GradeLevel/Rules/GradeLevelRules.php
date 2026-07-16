<?php

namespace App\Domains\GradeLevel\Rules;

use App\Domains\GradeLevel\Enums\EducationalCycle;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GradeLevelRules
{
    public static function create(): array
    {
        return [
            'program_id' => ['required', 'exists:programs,id'],
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50'],
            'short_name' => ['nullable', 'string', 'max:20'],
            'level_order' => ['required', 'integer', 'min:1'],
            'educational_cycle' => ['required', 'string', 'in:'.implode(',', array_column(EducationalCycle::cases(), 'value'))],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function update(?int $gradeLevelId = null): array
    {
        return [
            'program_id' => ['sometimes', 'required', 'exists:programs,id'],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'code' => ['sometimes', 'required', 'string', 'max:50'],
            'short_name' => ['nullable', 'string', 'max:20'],
            'level_order' => ['sometimes', 'required', 'integer', 'min:1'],
            'educational_cycle' => ['sometimes', 'required', 'string', 'in:'.implode(',', array_column(EducationalCycle::cases(), 'value'))],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $gradeLevelId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($gradeLevelId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
