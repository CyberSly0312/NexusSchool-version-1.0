<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Rules;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * An enrollment may only reference an academic year whose status is active.
 */
final class ActiveAcademicYearRule implements ValidationRule
{
    public function __construct(private readonly int $academicCalendarYearId)
    {
    }

    public function fails(): bool
    {
        $year = AcademicCalendarYear::query()->find($this->academicCalendarYearId);

        return $year === null || ! $year->isActive();
    }

    public function validate($attribute, mixed $value, Closure $fail): void
    {
        $year = AcademicCalendarYear::query()->find($this->academicCalendarYearId);

        if ($year === null) {
            $fail('The selected academic year does not exist.');

            return;
        }

        if (! $year->isActive()) {
            $fail('The selected academic year is not active.');
        }
    }
}
