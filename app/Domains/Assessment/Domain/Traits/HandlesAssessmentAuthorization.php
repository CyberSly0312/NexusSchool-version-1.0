<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorization for the Assessment engine (SPR-009).
 *
 * Access is granted exclusively through the assessment capabilities —
 * view / create / update / publish / score / statistics / manage — never
 * through role membership. The super_admin bypass is the platform-wide
 * superuser escape hatch shared by every domain policy. All capabilities are
 * school-scoped and group-parameterized so a single trait backs the
 * Assessment, Type, Session, Score and Template policies.
 */
trait HandlesAssessmentAuthorization
{
    abstract protected function permissionGroup(): string;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo($this->permissionGroup().'.view');
    }

    public function view(User $user, Model $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo($this->permissionGroup().'.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo($this->permissionGroup().'.create');
    }

    public function update(User $user, Model $model): bool
    {
        return $this->authorizeAssessmentAction($user, $model, 'update');
    }

    public function delete(User $user, Model $model): bool
    {
        return false;
    }

    public function restore(User $user, Model $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }

    public function publish(User $user, ?Model $model = null): bool
    {
        return $this->authorizeAssessmentAction($user, $model, 'publish');
    }

    public function score(User $user, ?Model $model = null): bool
    {
        return $this->authorizeAssessmentAction($user, $model, 'score');
    }

    public function statistics(User $user, ?Model $model = null): bool
    {
        return $this->authorizeAssessmentAction($user, $model, 'statistics');
    }

    public function manage(User $user, ?Model $model = null): bool
    {
        return $this->authorizeAssessmentAction($user, $model, 'manage');
    }

    private function authorizeAssessmentAction(User $user, ?Model $model, string $action): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($model !== null && $user->school_id !== $model->school_id) {
            return false;
        }

        return $user->hasPermissionTo($this->permissionGroup().'.'.$action);
    }
}
