<?php

declare(strict_types=1);

namespace App\Domains\System\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BackupRules
{
    public static function create(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'type' => ['sometimes', 'required', 'string', 'in:manual,scheduled,pre_update'],
            'disk' => ['sometimes', 'required', 'string', 'in:local,s3'],
            'tables_included' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
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
