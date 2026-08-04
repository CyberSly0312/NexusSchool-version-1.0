<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\Actions;

use App\Domains\Guardian\Domain\Events\PrimaryGuardianAssigned;
use App\Domains\Guardian\Domain\Exceptions\RelationshipStateException;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class AssignPrimaryGuardianAction
{
    public function execute(StudentGuardianRelationship $relationship, ?User $actor = null): StudentGuardianRelationship
    {
        if ($relationship->trashed()) {
            throw RelationshipStateException::unlinked($relationship->id);
        }

        return DB::transaction(function () use ($relationship, $actor): StudentGuardianRelationship {
            StudentGuardianRelationship::query()
                ->forSchool($relationship->school_id)
                ->forStudent($relationship->student_id)
                ->whereKeyNot($relationship->getKey())
                ->update(['is_primary' => false]);

            $relationship->update(['is_primary' => true]);

            $relationship->refresh();

            PrimaryGuardianAssigned::dispatch($relationship, $actor);

            return $relationship;
        });
    }
}
