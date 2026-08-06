<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Exceptions;

use App\Domains\Staff\Domain\Enums\StaffSpecialization;
use RuntimeException;

/**
 * Raised when a teacher-specific operation targets a staff member whose
 * specialization is not Teacher.
 */
final class TeacherSpecializationException extends RuntimeException
{
    public static function notTeacher(int $staffId, StaffSpecialization $specialization): self
    {
        return new self("Staff member {$staffId} is specialized as {$specialization->value} and is not a teacher.");
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
