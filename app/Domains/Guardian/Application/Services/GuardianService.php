<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\Services;

use App\Domains\Guardian\Application\Actions\AssignEmergencyContactAction;
use App\Domains\Guardian\Application\Actions\AssignPickupAuthorizationAction;
use App\Domains\Guardian\Application\Actions\AssignPrimaryGuardianAction;
use App\Domains\Guardian\Application\Actions\CreateGuardianAction;
use App\Domains\Guardian\Application\Actions\LinkGuardianAction;
use App\Domains\Guardian\Application\Actions\UnlinkGuardianAction;
use App\Domains\Guardian\Application\Actions\UpdateGuardianAction;
use App\Domains\Guardian\Application\DTOs\GuardianData;
use App\Domains\Guardian\Application\DTOs\RelationshipData;
use App\Domains\Guardian\Domain\ValueObjects\PickupWindow;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Models\User;

final class GuardianService
{
    public function __construct(
        private readonly CreateGuardianAction $createGuardianAction,
        private readonly UpdateGuardianAction $updateGuardianAction,
        private readonly LinkGuardianAction $linkGuardianAction,
        private readonly UnlinkGuardianAction $unlinkGuardianAction,
        private readonly AssignPrimaryGuardianAction $assignPrimaryGuardianAction,
        private readonly AssignEmergencyContactAction $assignEmergencyContactAction,
        private readonly AssignPickupAuthorizationAction $assignPickupAuthorizationAction,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(int $schoolId, array $attributes, ?User $actor = null): Guardian
    {
        $data = GuardianData::fromArray($attributes);

        return $this->createGuardianAction->execute($data, $actor);
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    public function update(Guardian $guardian, array $changes, ?User $actor = null): Guardian
    {
        return $this->updateGuardianAction->execute($guardian, $changes, $actor);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function link(int $schoolId, array $attributes, ?User $actor = null): StudentGuardianRelationship
    {
        $data = RelationshipData::fromArray($attributes);

        return $this->linkGuardianAction->execute($data, $actor);
    }

    public function unlink(
        StudentGuardianRelationship $relationship,
        ?string $reason = null,
        ?User $actor = null,
    ): StudentGuardianRelationship {
        return $this->unlinkGuardianAction->execute($relationship, $reason, $actor);
    }

    public function assignPrimary(
        StudentGuardianRelationship $relationship,
        ?User $actor = null,
    ): StudentGuardianRelationship {
        return $this->assignPrimaryGuardianAction->execute($relationship, $actor);
    }

    public function assignEmergencyContact(
        StudentGuardianRelationship $relationship,
        bool $grant = true,
        ?int $priority = null,
        ?User $actor = null,
    ): StudentGuardianRelationship {
        return $this->assignEmergencyContactAction->execute($relationship, $grant, $priority, $actor);
    }

    public function assignPickupAuthorization(
        StudentGuardianRelationship $relationship,
        bool $grant = true,
        ?PickupWindow $window = null,
        ?User $actor = null,
    ): StudentGuardianRelationship {
        return $this->assignPickupAuthorizationAction->execute($relationship, $grant, $window, $actor);
    }
}
