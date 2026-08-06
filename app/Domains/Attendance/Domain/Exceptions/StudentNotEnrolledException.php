<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Exceptions;

/**
 * Thrown when a student that holds no active enrollment is marked, or when a
 * session is generated for a class that has no active enrollments.
 */
final class StudentNotEnrolledException extends AttendanceException
{
    public static function forStudent(int|string $studentId, int|string $classId): self
    {
        return new self("Student [{$studentId}] is not actively enrolled in class [{$classId}].");
    }
}
