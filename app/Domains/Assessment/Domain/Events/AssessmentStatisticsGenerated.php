<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Events;

use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentStatistics;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssessmentStatisticsGenerated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Assessment $assessment,
        public readonly AssessmentStatistics $statistics,
        public readonly ?User $actor = null,
    ) {
    }
}
