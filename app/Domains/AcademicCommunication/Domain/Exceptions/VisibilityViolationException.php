<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Exceptions;

use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;

/**
 * Thrown when a viewer asks for a document (or a section of a document) that
 * their visibility scope does not grant. Self-scoped viewers (parents,
 * students) can never see another person's data through this engine.
 */
final class VisibilityViolationException extends AcademicCommunicationException
{
    public static function forScope(VisibilityScope $scope, string $section): self
    {
        return new self(
            sprintf(
                'Visibility scope [%s] is not allowed to see section [%s].',
                $scope->value,
                $section,
            ),
        );
    }

    public static function selfOnly(VisibilityScope $scope): self
    {
        return new self(
            sprintf(
                'Visibility scope [%s] is self-scoped and cannot access another person\'s document.',
                $scope->value,
            ),
        );
    }
}
