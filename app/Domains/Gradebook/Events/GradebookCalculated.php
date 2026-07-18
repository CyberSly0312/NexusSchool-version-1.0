<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Events;

use Illuminate\Foundation\Events\Dispatchable;

class GradebookCalculated
{
    use Dispatchable;

    public function __construct(
        public readonly int $studentId,
        public readonly int $academicTermId,
        public readonly int $academicYearId,
        public readonly ?float $termAverage,
        public readonly ?float $weightedAverage,
    ) {
    }
}
