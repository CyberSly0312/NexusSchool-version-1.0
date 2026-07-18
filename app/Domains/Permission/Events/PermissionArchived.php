<?php

declare(strict_types=1);

namespace App\Domains\Permission\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Permission;

class PermissionArchived
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Permission $permission,
    ) {
    }
}
