<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorization for certificate & transcript completion operations (SPR-015).
 *
 * Extends the SPR-002 school resource pattern with the completion capabilities
 * of the dedicated permission group: view, generate, review, approve, publish
 * and archive. Verification capabilities are deliberately reserved for SPR-016.
 */
trait HandlesCertificateAndTranscriptAuthorization
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

        return $user->school_id === $model->getAttribute('school_id') && $user->hasPermissionTo($this->permissionGroup().'.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo($this->permissionGroup().'.create');
    }

    public function update(User $user, Model $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->getAttribute('school_id') && $user->hasPermissionTo($this->permissionGroup().'.update');
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

    public function generate(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'generate');
    }

    public function review(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'review');
    }

    public function approve(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'approve');
    }

    public function publish(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'publish');
    }

    public function archive(User $user, Model $model): bool
    {
        return $this->authorizeLifecycle($user, $model, 'archive');
    }

    private function authorizeLifecycle(User $user, Model $model, string $action): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->school_id === $model->getAttribute('school_id') && $user->hasPermissionTo($this->permissionGroup().'.'.$action);
    }
}
