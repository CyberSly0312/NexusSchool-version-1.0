<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Infrastructure\Listeners;

use App\Domains\AcademicPolicy\Domain\Events\AcademicPolicyArchived;
use App\Domains\AcademicPolicy\Domain\Events\AcademicPolicyCreated;
use App\Domains\AcademicPolicy\Domain\Events\AcademicPolicyUpdated;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;
use App\Models\AuditLog;
use App\Models\User;

final class AcademicPolicyAuditLogger
{
    public function handleCreated(AcademicPolicyCreated $event): void
    {
        $this->log($event->policy, 'created', $event->actor);
    }

    public function handleUpdated(AcademicPolicyUpdated $event): void
    {
        $this->log($event->policy, 'updated', $event->actor);
    }

    public function handleArchived(AcademicPolicyArchived $event): void
    {
        $this->log($event->policy, 'archived', $event->actor);
    }

    private function log(AcademicPolicy $model, string $action, ?User $actor = null): void
    {
        if (! (bool) config('nexus-academic-policy.policy.audit', true)) {
            return;
        }

        AuditLog::create([
            'school_id' => $model->school_id,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'event' => class_basename($model),
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);
    }
}
