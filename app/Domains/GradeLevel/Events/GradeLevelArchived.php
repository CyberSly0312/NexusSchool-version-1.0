<?php

namespace App\Domains\GradeLevel\Events;

use App\Models\GradeLevel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GradeLevelArchived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly GradeLevel $gradeLevel,
    ) {}
}
