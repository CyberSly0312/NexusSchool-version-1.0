<?php

namespace App\Domains\Classroom\Events;

use App\Models\AcademicClass;
use App\Models\Classroom;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClassroomAssigned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Classroom $classroom,
        public readonly AcademicClass $academicClass,
    ) {}
}
