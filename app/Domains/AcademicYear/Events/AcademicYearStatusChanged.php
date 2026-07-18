<?php

declare(strict_types=1);

namespace App\Domains\AcademicYear\Events;

use App\Domains\AcademicYear\Enums\AcademicYearStatus;
use App\Models\AcademicYear;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcademicYearStatusChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AcademicYear $academicYear,
        public readonly AcademicYearStatus $oldStatus,
        public readonly AcademicYearStatus $newStatus,
    ) {
    }
}
