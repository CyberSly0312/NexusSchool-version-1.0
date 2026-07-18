<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Events;

use Illuminate\Foundation\Events\Dispatchable;

class RankingsCalculated
{
    use Dispatchable;

    public function __construct(
        public readonly int $assessmentId,
        public readonly int $studentCount,
    ) {
    }
}
