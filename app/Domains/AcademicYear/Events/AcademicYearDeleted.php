<?php

namespace App\Domains\AcademicYear\Events;

use App\Models\AcademicYear;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcademicYearDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly AcademicYear $academicYear,
    ) {}
}
