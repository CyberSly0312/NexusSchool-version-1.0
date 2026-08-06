<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Infrastructure\Policies;

use App\Domains\ClassCouncil\Domain\Traits\HandlesClassCouncilAuthorization;

final class CouncilSessionPolicy
{
    use HandlesClassCouncilAuthorization;

    protected function permissionGroup(): string
    {
        return 'class-council';
    }
}
