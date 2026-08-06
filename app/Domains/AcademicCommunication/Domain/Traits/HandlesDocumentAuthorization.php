<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorization for the Academic Communication Engine (SPR-012).
 *
 * Access is granted exclusively through the document capabilities — view /
 * generate / review / approve / publish / archive / template.manage — never
 * through role membership. The super_admin bypass is the platform-wide
 * superuser escape hatch shared by every domain policy. All capabilities are
 * school-scoped, so a user of one school can never touch another school's
 * documents.
 */
trait HandlesDocumentAuthorization
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('document.view');
    }

    public function view(User $user, Model $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->school_id && $user->hasPermissionTo('document.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('document.generate');
    }

    public function update(User $user, Model $model): bool
    {
        return $this->authorizeDocumentAction($user, $model, 'generate');
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

    public function generate(User $user, ?Model $model = null): bool
    {
        return $this->authorizeDocumentAction($user, $model, 'generate');
    }

    public function review(User $user, ?Model $model = null): bool
    {
        return $this->authorizeDocumentAction($user, $model, 'review');
    }

    public function approve(User $user, ?Model $model = null): bool
    {
        return $this->authorizeDocumentAction($user, $model, 'approve');
    }

    public function publish(User $user, ?Model $model = null): bool
    {
        return $this->authorizeDocumentAction($user, $model, 'publish');
    }

    public function archive(User $user, ?Model $model = null): bool
    {
        return $this->authorizeDocumentAction($user, $model, 'archive');
    }

    public function templateManage(User $user, ?Model $model = null): bool
    {
        return $this->authorizeDocumentAction($user, $model, 'template.manage');
    }

    private function authorizeDocumentAction(User $user, ?Model $model, string $action): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($model !== null && $user->school_id !== $model->school_id) {
            return false;
        }

        return $user->hasPermissionTo('document.'.$action);
    }
}
