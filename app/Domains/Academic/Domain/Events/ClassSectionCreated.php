<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Events;

use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClassSectionCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly ClassSection $section,
        public readonly ?User $actor = null,
    ) {
    }
}
