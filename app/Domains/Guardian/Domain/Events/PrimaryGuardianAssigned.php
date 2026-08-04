<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Events;

use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrimaryGuardianAssigned
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly StudentGuardianRelationship $relationship,
        public readonly ?User $actor = null,
    ) {
    }
}
