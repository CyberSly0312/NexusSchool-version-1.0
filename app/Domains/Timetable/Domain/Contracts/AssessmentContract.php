<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Contracts;

use App\Domains\Timetable\Domain\References\AssessmentReference;

/**
 * Read-only gateway to the future Assessment engine. The interface exists today
 * so the scheduling engine can book assessment slots without depending on any
 * Assessment model; adapters will be swapped in when the engine lands.
 */
interface AssessmentContract
{
    /**
     * @return list<AssessmentReference>
     */
    public function assessments(string $schoolId): array;

    public function assessment(string $schoolId, string $assessmentId): ?AssessmentReference;
}
