<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\DTOs;

use App\Domains\Staff\Domain\Enums\AssignmentType;

/**
 * Data transfer object for a time-aware staff assignment (department, position
 * or campus). Assignments carry a validity window and never overwrite the
 * immutable employment history.
 */
final readonly class AssignmentData
{
    public function __construct(
        public string $schoolId,
        public int $staffId,
        public AssignmentType $assignmentType,
        public string $assignmentStartDate,
        public ?int $employmentId = null,
        public ?int $departmentId = null,
        public ?int $positionId = null,
        public ?string $campusSchoolId = null,
        public ?string $assignmentEndDate = null,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            staffId: (int) $data['staff_id'],
            assignmentType: AssignmentType::from((string) $data['assignment_type']),
            assignmentStartDate: (string) $data['assignment_start_date'],
            employmentId: isset($data['employment_id']) ? (int) $data['employment_id'] : null,
            departmentId: isset($data['department_id']) ? (int) $data['department_id'] : null,
            positionId: isset($data['position_id']) ? (int) $data['position_id'] : null,
            campusSchoolId: self::nullableString($data['campus_school_id'] ?? null),
            assignmentEndDate: self::nullableString($data['assignment_end_date'] ?? null),
            notes: self::nullableString($data['notes'] ?? null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'staff_id' => $this->staffId,
            'employment_id' => $this->employmentId,
            'assignment_type' => $this->assignmentType->value,
            'department_id' => $this->departmentId,
            'position_id' => $this->positionId,
            'campus_school_id' => $this->campusSchoolId,
            'assignment_start_date' => $this->assignmentStartDate,
            'assignment_end_date' => $this->assignmentEndDate,
            'is_current' => true,
            'notes' => $this->notes,
        ];
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
