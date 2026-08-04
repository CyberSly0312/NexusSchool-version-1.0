<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\Actions;

use App\Domains\Guardian\Domain\Events\PickupAuthorizationGranted;
use App\Domains\Guardian\Domain\Exceptions\RelationshipStateException;
use App\Domains\Guardian\Domain\ValueObjects\PickupWindow;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class AssignPickupAuthorizationAction
{
    /**
     * Grants (or revokes) the authorized-pickup role on a relationship.
     * A {@see PickupWindow} restricts when the pickup is valid.
     */
    public function execute(
        StudentGuardianRelationship $relationship,
        bool $grant = true,
        ?PickupWindow $window = null,
        ?User $actor = null,
    ): StudentGuardianRelationship {
        if ($relationship->trashed()) {
            throw RelationshipStateException::unlinked($relationship->id);
        }

        return DB::transaction(function () use ($relationship, $grant, $window, $actor): StudentGuardianRelationship {
            $relationship->update([
                'is_authorized_pickup' => $grant,
                'pickup_valid_from' => $grant ? $window?->validFrom : null,
                'pickup_valid_until' => $grant ? $window?->validUntil : null,
            ]);

            $relationship->refresh();

            PickupAuthorizationGranted::dispatch(
                $relationship,
                $grant,
                $window?->validFrom,
                $window?->validUntil,
                $actor,
            );

            return $relationship;
        });
    }
}
