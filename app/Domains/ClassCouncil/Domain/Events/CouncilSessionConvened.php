<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Events;

use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CouncilSessionConvened
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  list<object>  $reviews
     */
    public function __construct(
        public readonly CouncilSession $session,
        public readonly array $reviews = [],
        public readonly ?User $actor = null,
    ) {
    }
}
