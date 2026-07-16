<?php

namespace App\Domains\AcademicTerm\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AcademicTermRules
{
    public static function create(): array
    {
        return [
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'school_id' => ['required', 'exists:tenants,id'],
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:draft,active,archived'],
        ];
    }

    public static function update(?int $academicTermId = null): array
    {
        return [
            'academic_year_id' => ['sometimes', 'required', 'exists:academic_years,id'],
            'school_id' => ['sometimes', 'required', 'exists:tenants,id'],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:draft,active,archived'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $academicTermId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($academicTermId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
