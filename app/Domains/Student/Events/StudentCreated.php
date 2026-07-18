<?php

declare(strict_types=1);

namespace App\Domains\Student\Events;

use App\Models\Student;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Student $student,
    ) {
    }
}
