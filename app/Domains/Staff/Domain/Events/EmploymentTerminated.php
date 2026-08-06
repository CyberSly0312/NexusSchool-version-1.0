<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Events;

use App\Domains\Staff\Infrastructure\Models\Employment;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmploymentTerminated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Employment $employment,
        public readonly ?string $reason = null,
        public readonly ?string $terminationDate = null,
        public readonly ?User $actor = null,
    ) {
    }
}
