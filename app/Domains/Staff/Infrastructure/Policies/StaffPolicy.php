<?php

declare(strict_types=1);

namespace App\Domains\Staff\Infrastructure\Policies;

use App\Domains\Staff\Domain\Traits\HandlesStaffAuthorization;

final class StaffPolicy
{
    use HandlesStaffAuthorization;

    protected function permissionGroup(): string
    {
        return 'staff';
    }
}
