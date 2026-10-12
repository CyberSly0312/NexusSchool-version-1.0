<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Infrastructure\Policies;

use App\Domains\AcademicPolicy\Domain\Traits\HandlesAcademicPolicyAuthorization;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;
use App\Models\User;

final class AcademicPolicyPolicy
{
    use HandlesAcademicPolicyAuthorization;

    protected function permissionGroup(): string
    {
        return 'academic_policy';
    }

    public function restore(User $user, AcademicPolicy $policy): bool
    {
        return false;
    }

    public function forceDelete(User $user, AcademicPolicy $policy): bool
    {
        return false;
    }
}
