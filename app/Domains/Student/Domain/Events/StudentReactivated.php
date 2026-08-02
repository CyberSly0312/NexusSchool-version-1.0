<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\Events;

use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentReactivated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Student $student,
        public readonly ?User $actor = null,
    ) {
    }
}
