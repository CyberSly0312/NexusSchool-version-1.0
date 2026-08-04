<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Rules;

use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Enforces the core SPR-004 invariant: a student can hold at most one ACTIVE
 * enrollment per academic year. Archived/withdrawn/graduated rows do not
 * count, so promotion and re-enrollment history is preserved.
 */
final class UniqueActiveEnrollmentRule implements ValidationRule
{
    public function __construct(
        private readonly int $studentId,
        private readonly int $academicCalendarYearId,
        private readonly ?int $ignoreId = null,
    ) {
    }

    public function fails(): bool
    {
        return Enrollment::query()
            ->where('student_id', $this->studentId)
            ->where('academic_calendar_year_id', $this->academicCalendarYearId)
            ->where('status', EnrollmentStatus::Active)
            ->when($this->ignoreId !== null, fn ($query) => $query->whereKeyNot($this->ignoreId))
            ->exists();
    }

    public function validate($attribute, mixed $value, Closure $fail): void
    {
        if ($this->fails()) {
            $fail('A student can only have one active enrollment per academic year.');
        }
    }
}
