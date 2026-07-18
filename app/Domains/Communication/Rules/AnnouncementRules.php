<?php

declare(strict_types=1);

namespace App\Domains\Communication\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AnnouncementRules
{
    public static function validate(array $data, string $mode = 'create'): array
    {
        $rules = self::rules($mode);
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $validator->validated();
    }

    public static function rules(string $mode = 'create'): array
    {
        $required = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'school_id' => [$required, 'string'],
            'author_id' => [$required],
            'title' => [$required, 'string', 'max:255'],
            'body' => [$required, 'string'],
            'scope' => ['nullable', 'string', 'in:school_wide,department,grade_level,class,staff,parents,individual'],
            'target_id' => ['nullable', 'string'],
            'priority' => ['nullable', 'string', 'in:low,normal,high,urgent'],
            'is_pinned' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', 'in:draft,published,archived'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:published_at'],
        ];
    }
}
