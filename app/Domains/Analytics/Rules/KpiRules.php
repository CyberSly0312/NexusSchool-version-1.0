<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class KpiRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'formula' => ['nullable', 'array'],
            'target_value' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'status' => ['sometimes', 'string', 'in:active,inactive,archived'],
            'calculation_frequency' => ['nullable', 'string', 'in:daily,weekly,monthly,quarterly,yearly'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['sometimes', 'required', 'string', 'max:100'],
            'formula' => ['nullable', 'array'],
            'target_value' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'status' => ['sometimes', 'string', 'in:active,inactive,archived'],
            'calculation_frequency' => ['nullable', 'string', 'in:daily,weekly,monthly,quarterly,yearly'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?string $id = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($id),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
