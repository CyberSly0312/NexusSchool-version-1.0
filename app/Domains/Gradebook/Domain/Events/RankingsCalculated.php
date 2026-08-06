<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Events;

use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RankingsCalculated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Gradebook $gradebook,
        public readonly ?User $actor = null,
    ) {
    }
}
