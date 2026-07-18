<?php

declare(strict_types=1);

namespace App\Domains\AcademicClass\Events;

use App\Models\AcademicClass;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcademicClassActivated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AcademicClass $academicClass,
    ) {
    }
}
