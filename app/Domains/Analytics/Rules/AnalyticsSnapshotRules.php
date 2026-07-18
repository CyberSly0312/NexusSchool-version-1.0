<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AnalyticsSnapshotRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'snapshot_type' => ['required', 'string', 'max:100'],
            'data' => ['required', 'array'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'generated_at' => ['nullable', 'date'],
            'is_immutable' => ['sometimes', 'boolean'],
        ];
    }

    public static function validate(array $data, string $mode = 'create'): array
    {
        $validator = Validator::make($data, self::create());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
