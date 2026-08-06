<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Infrastructure\Policies;

use App\Domains\Gradebook\Domain\Traits\HandlesGradebookAuthorization;

final class GradeEntryPolicy
{
    use HandlesGradebookAuthorization;

    protected function permissionGroup(): string
    {
        return 'gradebook';
    }
}
