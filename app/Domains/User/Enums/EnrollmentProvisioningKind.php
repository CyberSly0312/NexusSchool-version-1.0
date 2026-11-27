<?php

declare(strict_types=1);

namespace App\Domains\User\Enums;

/**
 * The only two enrollment-linked account kinds approved for Task 11.2.
 *
 * These values describe a provisioning workflow, not a panel authorization or
 * a child relationship. They never make an account active or grant access to a
 * student record.
 */
enum EnrollmentProvisioningKind: string
{
    case Student = 'student';
    case Parent = 'parent';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Élève',
            self::Parent => 'Parent',
        };
    }
}
