<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Exceptions;

/**
 * Thrown when an override of an automatic decision is recorded without the
 * mandatory justification or approval (SPR-013). Every override requires a
 * justification, an approval and an audit history.
 */
final class OverrideApprovalRequiredException extends ClassCouncilException
{
    public static function justification(): self
    {
        return new self('An override requires a written justification.');
    }

    public static function approval(): self
    {
        return new self('An override requires an approval before it can be recorded.');
    }
}
