<?php

declare(strict_types=1);

namespace App\Domains\SubjectCoefficient\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SubjectCoefficientRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'grade_level_id' => ['required', 'integer', 'exists:grade_levels,id'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'coefficient' => ['required', 'numeric', 'min:0.01', 'max:99.99'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after:effective_from'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function update(?int $subjectCoefficientId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'subject_id' => ['sometimes', 'required', 'integer', 'exists:subjects,id'],
            'program_id' => ['sometimes', 'required', 'integer', 'exists:programs,id'],
            'grade_level_id' => ['sometimes', 'required', 'integer', 'exists:grade_levels,id'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'coefficient' => ['sometimes', 'required', 'numeric', 'min:0.01', 'max:99.99'],
            'effective_from' => ['sometimes', 'required', 'date'],
            'effective_to' => ['nullable', 'date', 'after:effective_from'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $subjectCoefficientId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($subjectCoefficientId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
