<?php

declare(strict_types=1);

namespace App\Domains\Api\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WebhookRules
{
    public static function create(): array
    {
        return [
            'url' => ['required', 'url', 'max:500'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string'],
            'description' => ['nullable', 'string'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'url' => ['sometimes', 'required', 'url', 'max:500'],
            'events' => ['sometimes', 'required', 'array', 'min:1'],
            'events.*' => ['string'],
            'description' => ['nullable', 'string'],
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
