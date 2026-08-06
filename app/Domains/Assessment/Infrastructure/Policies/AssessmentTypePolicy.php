<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Policies;

use App\Domains\Assessment\Domain\Traits\HandlesAssessmentAuthorization;

final class AssessmentTypePolicy
{
    use HandlesAssessmentAuthorization;

    protected function permissionGroup(): string
    {
        return 'assessment';
    }
}
