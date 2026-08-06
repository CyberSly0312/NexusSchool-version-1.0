<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\DTOs;

/**
 * Data transfer object for the auto-generation pass (SPR-007). The engine never
 * derives the workload itself: callers supply weekly lesson counts per subject
 * (or rely on the configured default) so the Timetable domain stays decoupled
 * from the Academic domain's coefficient rules.
 */
final readonly class GenerateTimetableData
{
    /**
     * @param  list<int|string>|null  $classIds  classes to schedule; empty = every class of the school
     * @param  array<int|string, int>|null  $requirements  subject id => lessons per week
     */
    public function __construct(
        public string $schoolId,
        public int|string $timetableId,
        public ?array $classIds = null,
        public ?array $requirements = null,
        public ?int $lessonDurationMinutes = null,
    ) {
    }

    public function lessonsPerWeek(int|string $subjectId, int $default): int
    {
        if ($this->requirements !== null && isset($this->requirements[$subjectId])) {
            return max(1, (int) $this->requirements[$subjectId]);
        }

        return $default;
    }
}
