<?php

namespace App\Domains\Gradebook\Events;

use Illuminate\Foundation\Events\Dispatchable;

class AssessmentResultsPublished
{
    use Dispatchable;

    public function __construct(
        public readonly array $resultIds,
        public readonly int $assessmentId,
    ) {}
}
