<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\DTOs;

use App\Domains\Timetable\Domain\Enums\TimetableStatus;

/**
 * Data transfer object for creating a Timetable aggregate (SPR-007).
 */
final readonly class CreateTimetableData
{
    public function __construct(
        public string $schoolId,
        public string $name,
        public int|string|null $academicYearId = null,
        public int|string|null $academicTermId = null,
        public ?string $code = null,
        public ?string $description = null,
        public TimetableStatus $status = TimetableStatus::Draft,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            name: (string) $data['name'],
            academicYearId: self::nullableIntOrString($data['academic_calendar_year_id'] ?? null),
            academicTermId: self::nullableIntOrString($data['academic_calendar_term_id'] ?? null),
            code: self::nullableString($data['code'] ?? null),
            description: self::nullableString($data['description'] ?? null),
            status: isset($data['status']) ? TimetableStatus::from((string) $data['status']) : TimetableStatus::Draft,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'name' => $this->name,
            'academic_calendar_year_id' => $this->academicYearId,
            'academic_calendar_term_id' => $this->academicTermId,
            'code' => $this->code,
            'description' => $this->description,
            'status' => $this->status->value,
            'version' => 1,
        ];
    }

    private static function nullableIntOrString(mixed $value): int|string|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : (string) $value;
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
