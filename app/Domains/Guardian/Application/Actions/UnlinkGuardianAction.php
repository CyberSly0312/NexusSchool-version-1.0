<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\Actions;

use App\Domains\Guardian\Domain\Events\GuardianUnlinked;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UnlinkGuardianAction
{
    public function execute(
        StudentGuardianRelationship $relationship,
        ?string $reason = null,
        ?User $actor = null,
    ): StudentGuardianRelationship {
        return DB::transaction(function () use ($relationship, $reason, $actor): StudentGuardianRelationship {
            $relationship->update([
                'relationship_end_date' => now()->toDateString(),
                'is_primary' => false,
                'is_emergency_contact' => false,
                'is_authorized_pickup' => false,
            ]);

            $relationship->delete();

            GuardianUnlinked::dispatch($relationship, $reason, $actor);

            return $relationship;
        });
    }
}
