<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Domain\Events;

use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcademicPolicyUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AcademicPolicy $policy,
        public readonly ?User $actor = null,
    ) {
    }
}
