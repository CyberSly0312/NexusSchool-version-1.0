<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Events;

use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssessmentArchived
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Assessment $assessment,
        public readonly ?User $actor = null,
    ) {
    }
}
