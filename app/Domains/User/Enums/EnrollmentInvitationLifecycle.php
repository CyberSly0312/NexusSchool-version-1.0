<?php

declare(strict_types=1);

namespace App\Domains\User\Enums;

/**
 * Safe, non-secret classification of an invitation lifecycle operation.
 */
enum EnrollmentInvitationLifecycle: string
{
    case Issued = 'issued';
    case Reused = 'reused';
    case Reissued = 'reissued';
}
