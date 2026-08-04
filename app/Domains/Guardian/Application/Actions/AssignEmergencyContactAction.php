<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\Actions;

use App\Domains\Guardian\Domain\Events\EmergencyContactAssigned;
use App\Domains\Guardian\Domain\Exceptions\RelationshipStateException;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class AssignEmergencyContactAction
{
    /**
     * Grants (or revokes) the emergency-contact role on a relationship.
     * Multiple emergency contacts per student are allowed.
     */
    public function execute(
        StudentGuardianRelationship $relationship,
        bool $grant = true,
        ?int $priority = null,
        ?User $actor = null,
    ): StudentGuardianRelationship {
        if ($relationship->trashed()) {
            throw RelationshipStateException::unlinked($relationship->id);
        }

        return DB::transaction(function () use ($relationship, $grant, $priority, $actor): StudentGuardianRelationship {
            $relationship->update([
                'is_emergency_contact' => $grant,
                'emergency_contact_priority' => $grant ? $priority : null,
            ]);

            $relationship->refresh();

            EmergencyContactAssigned::dispatch($relationship, $grant, $priority, $actor);

            return $relationship;
        });
    }
}
