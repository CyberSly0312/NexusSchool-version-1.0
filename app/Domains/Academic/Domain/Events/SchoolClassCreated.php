<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Events;

use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SchoolClassCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly SchoolClass $schoolClass,
        public readonly ?User $actor = null,
    ) {
    }
}
