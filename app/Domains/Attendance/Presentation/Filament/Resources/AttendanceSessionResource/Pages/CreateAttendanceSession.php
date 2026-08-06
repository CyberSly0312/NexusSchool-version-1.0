<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources\AttendanceSessionResource\Pages;

use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceSessionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendanceSession extends CreateRecord
{
    protected static string $resource = AttendanceSessionResource::class;

    protected static ?string $title = 'Créer une séance';
}
