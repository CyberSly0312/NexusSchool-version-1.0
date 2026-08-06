<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Exceptions;

/**
 * Raised when a schedule entry cannot be created or modified: overlapping
 * windows, a class double-booked in one period, a mismatch between the lesson
 * duration and the teaching period, etc.
 */
class ScheduleEntryException extends TimetableException
{
}
