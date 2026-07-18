<?php

declare(strict_types=1);

namespace App\Domains\Shared\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

trait UniqueValidator
{
    protected function validateUniquePerSchool(Model $model, string $schoolId, string $field, string $value, ?int $excludeId = null): void
    {
        $query = $model->newQuery()->where('school_id', $schoolId)->where($field, $value);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        if ($query->exists()) {
            throw ValidationException::withMessages([
                $field => ["The {$field} has already been taken in this school."],
            ]);
        }
    }

    protected function validateUniquePerParent(Model $model, string $parentField, string $parentId, string $field, string $value, ?int $excludeId = null): void
    {
        $query = $model->newQuery()->where($parentField, $parentId)->where($field, $value);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        if ($query->exists()) {
            throw ValidationException::withMessages([
                $field => ["The {$field} has already been taken."],
            ]);
        }
    }
}
