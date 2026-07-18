<?php

declare(strict_types=1);

namespace App\Domains\Transport\Events;

use App\Models\StudentTransportAssignment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentUnassigned
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly StudentTransportAssignment $assignment,
    ) {
    }
}
