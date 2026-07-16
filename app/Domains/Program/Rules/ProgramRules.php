<?php

namespace App\Domains\Program\Rules;

use App\Domains\Program\Enums\EducationalLevel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProgramRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'exists:tenants,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'educational_level' => ['required', 'string', 'in:'.implode(',', array_column(EducationalLevel::cases(), 'value'))],
            'duration' => ['required', 'string', 'max:50'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function update(?int $programId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'exists:tenants,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'code' => ['sometimes', 'required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'educational_level' => ['sometimes', 'required', 'string', 'in:'.implode(',', array_column(EducationalLevel::cases(), 'value'))],
            'duration' => ['sometimes', 'required', 'string', 'max:50'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $programId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($programId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
