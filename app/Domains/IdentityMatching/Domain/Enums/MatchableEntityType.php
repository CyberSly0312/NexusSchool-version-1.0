<?php

declare(strict_types=1);

namespace App\Domains\IdentityMatching\Domain\Enums;

/**
 * Entity families supported by the shared school-scoped matching foundation.
 *
 * User means a parent/candidate account for identity resolution purposes. It
 * does not grant a role or change accounts; role assignment remains owned by
 * the authoritative role services.
 */
enum MatchableEntityType: string
{
    case Student = 'student';
    case Guardian = 'guardian';
    case User = 'user';
}
