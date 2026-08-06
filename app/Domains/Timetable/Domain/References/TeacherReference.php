<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\References;

/**
 * Read-only projection of a teacher (a Staff specialization) owned by the Staff
 * domain. Free of any concrete model.
 */
final readonly class TeacherReference
{
    public function __construct(
        public int|string $id,
        public string $fullName,
        public string $firstName,
        public string $lastName,
        /** @var list<int|string> */
        public array $subjectIds = [],
        public ?int $maxWeeklyHours = null,
        public bool $isActive = true,
    ) {
    }

    /**
     * @return list<int|string>
     */
    public function subjectIds(): array
    {
        return $this->subjectIds;
    }

    public function canTeach(int|string $subjectId): bool
    {
        return in_array($subjectId, $this->subjectIds, true);
    }
}
