<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Enums;

/**
 * Child-data operations that can be requested through a Parent surface.
 *
 * Only `View` is permitted in Task 11.3. Any other operation fails closed so a
 * future surface cannot accidentally inherit child access by omitting an
 * authorization check.
 */
enum GuardianChildOperation: string
{
    case View = 'view';
    case Download = 'download';
    case Edit = 'edit';

    public function isPermitted(): bool
    {
        return $this === self::View;
    }
}
