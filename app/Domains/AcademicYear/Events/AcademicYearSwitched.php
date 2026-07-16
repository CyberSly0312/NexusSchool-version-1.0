<?php

namespace App\Domains\AcademicYear\Events;

use App\Domains\School\Models\School;
use App\Models\AcademicYear;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcademicYearSwitched
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly School $school,
        public readonly ?AcademicYear $previousYear,
        public readonly AcademicYear $newYear,
    ) {}
}
