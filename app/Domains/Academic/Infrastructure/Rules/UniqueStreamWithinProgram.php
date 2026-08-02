<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Rules;

use App\Domains\Academic\Infrastructure\Models\Stream;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * A stream name must be unique within its program for the school.
 */
final class UniqueStreamWithinProgram implements ValidationRule
{
    public function __construct(
        private readonly string $schoolId,
        private readonly int $programId,
        private readonly ?int $ignoreId = null,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = Stream::query()
            ->forSchool($this->schoolId)
            ->where('academic_program_id', $this->programId)
            ->where('name', $value)
            ->when($this->ignoreId !== null, fn ($query) => $query->whereKeyNot($this->ignoreId))
            ->exists();

        if ($exists) {
            $fail('Une filière portant ce nom existe déjà dans ce programme.');
        }
    }
}
