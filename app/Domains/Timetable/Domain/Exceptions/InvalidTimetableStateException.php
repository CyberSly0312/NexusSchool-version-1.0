<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Exceptions;

/**
 * Raised when an operation is applied to a timetable whose lifecycle status does
 * not allow it (e.g. editing or publishing an archived timetable).
 */
class InvalidTimetableStateException extends TimetableException
{
}
