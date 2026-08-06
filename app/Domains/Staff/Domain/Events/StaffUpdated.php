<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Events;

use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StaffUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Staff $staff,
        public readonly ?User $actor = null,
    ) {
    }
}
