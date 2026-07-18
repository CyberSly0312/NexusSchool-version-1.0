<?php

declare(strict_types=1);

namespace App\Domains\Subject\Events;

use App\Models\Subject;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubjectUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Subject $subject,
    ) {
    }
}
