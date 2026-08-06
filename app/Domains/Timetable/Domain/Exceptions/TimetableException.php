<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Exceptions;

use RuntimeException;

/**
 * Marker base for every failure raised by the Timetable engine.
 */
class TimetableException extends RuntimeException
{
}
