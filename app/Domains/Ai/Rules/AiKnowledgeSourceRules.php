<?php

declare(strict_types=1);

namespace App\Domains\Ai\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AiKnowledgeSourceRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:markdown,pdf_metadata,database,text'],
            'description' => ['nullable', 'string'],
            'source_path' => ['nullable', 'string', 'max:500'],
            'source_type' => ['nullable', 'string', 'max:100'],
            'content_type' => ['nullable', 'string', 'max:100'],
            'configuration' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'source_path' => ['nullable', 'string', 'max:500'],
            'configuration' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
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
