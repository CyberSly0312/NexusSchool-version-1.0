<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Events;

use App\Domains\Guardian\Application\Services\GuardianLinkProvenance;
use App\Domains\Guardian\Domain\Enums\GuardianAccessDenialReason;
use App\Domains\Guardian\Domain\Exceptions\GuardianRelationshipVerificationException;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class GuardianLinked
{
    use Dispatchable;
    use SerializesModels;

    public readonly User $actor;

    /** @var array{provenance_version: int, provenance_signature: string} */
    public readonly array $provenancePayload;

    public function __construct(
        public readonly StudentGuardianRelationship $relationship,
    ) {
        $actor = Auth::user();

        if (! $actor instanceof User) {
            throw GuardianRelationshipVerificationException::because(
                GuardianAccessDenialReason::ActorNotAuthenticated,
            );
        }

        $this->actor = $actor;
        $this->provenancePayload = GuardianLinkProvenance::takeFor($relationship, $actor);
    }
}
