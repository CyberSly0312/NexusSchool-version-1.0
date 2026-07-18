<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReportRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:100'],
            'format' => ['required', 'string', 'in:pdf,excel,csv'],
            'status' => ['sometimes', 'string', 'in:draft,scheduled,generating,completed,failed'],
            'config' => ['nullable', 'array'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'scheduled_frequency' => ['nullable', 'string', 'in:daily,weekly,monthly,quarterly,yearly'],
            'generated_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', 'required', 'string', 'max:100'],
            'format' => ['sometimes', 'required', 'string', 'in:pdf,excel,csv'],
            'status' => ['sometimes', 'string', 'in:draft,scheduled,generating,completed,failed'],
            'config' => ['nullable', 'array'],
            'scheduled_at' => ['nullable', 'date'],
            'scheduled_frequency' => ['nullable', 'string', 'in:daily,weekly,monthly,quarterly,yearly'],
            'generated_by' => ['nullable', 'integer', 'exists:users,id'],
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
