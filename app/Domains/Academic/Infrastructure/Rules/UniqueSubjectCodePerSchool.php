<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Rules;

use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * A subject code must be unique within the school.
 */
final class UniqueSubjectCodePerSchool implements ValidationRule
{
    public function __construct(
        private readonly string $schoolId,
        private readonly ?int $ignoreId = null,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || trim((string) $value) === '') {
            return;
        }

        $exists = AcademicSubject::query()
            ->forSchool($this->schoolId)
            ->where('code', $value)
            ->when($this->ignoreId !== null, fn ($query) => $query->whereKeyNot($this->ignoreId))
            ->exists();

        if ($exists) {
            $fail('Le code de matière doit être unique au sein de l\'établissement.');
        }
    }
}
