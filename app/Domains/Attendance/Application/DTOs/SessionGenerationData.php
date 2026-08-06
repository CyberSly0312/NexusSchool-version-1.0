<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\DTOs;

use Carbon\CarbonImmutable;

/**
 * Data transfer object for generating attendance sessions (SPR-008).
 *
 * Generation creates one Draft session per class + time slot on the target
 * dates. The engine never derives which slots exist: it asks the
 * TimetableContract.
 *
 * @param  list<string>  $dates
 */
final readonly class SessionGenerationData
{
    public function __construct(
        public string $schoolId,
        public int|string $classId,
        public array $dates = [],
        public int|string|null $academicYearId = null,
        public int|string|null $academicTermId = null,
        public int|string|null $timetableId = null,
        public ?string $fromDate = null,
        public ?string $toDate = null,
    ) {
    }

    /**
     * Resolves the concrete dates either from the explicit list or from the
     * inclusive from/to window.
     *
     * @return list<string>
     */
    public function dates(): array
    {
        if ($this->dates !== []) {
            return array_values(array_unique($this->dates));
        }

        if ($this->fromDate === null || $this->toDate === null) {
            return [];
        }

        $dates = [];
        $cursor = CarbonImmutable::parse($this->fromDate);
        $end = CarbonImmutable::parse($this->toDate);

        while (! $cursor->greaterThan($end)) {
            $dates[] = $cursor->toDateString();
            $cursor = $cursor->addDay();
        }

        return $dates;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $dates = array_values(array_filter(
            array_map('strval', (array) ($data['dates'] ?? [])),
            static fn (string $date): bool => $date !== '',
        ));

        return new self(
            schoolId: (string) $data['school_id'],
            classId: (int) $data['school_class_id'],
            dates: $dates,
            academicYearId: self::nullableId($data['academic_calendar_year_id'] ?? null),
            academicTermId: self::nullableId($data['academic_calendar_term_id'] ?? null),
            timetableId: self::nullableId($data['timetable_id'] ?? null),
            fromDate: self::nullableString($data['from_date'] ?? null),
            toDate: self::nullableString($data['to_date'] ?? null),
        );
    }

    private static function nullableId(mixed $value): int|string|null
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
