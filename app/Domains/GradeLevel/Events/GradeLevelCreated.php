<?php

declare(strict_types=1);

namespace App\Domains\GradeLevel\Events;

use App\Models\GradeLevel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GradeLevelCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly GradeLevel $gradeLevel,
    ) {
    }
}
