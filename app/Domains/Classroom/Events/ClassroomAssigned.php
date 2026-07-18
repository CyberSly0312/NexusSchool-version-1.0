<?php

declare(strict_types=1);

namespace App\Domains\Classroom\Events;

use App\Models\AcademicClass;
use App\Models\Classroom;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClassroomAssigned
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Classroom $classroom,
        public readonly AcademicClass $academicClass,
    ) {
    }
}
