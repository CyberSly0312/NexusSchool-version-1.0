<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources\AttendanceRecordResource\Pages;

use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendanceRecord extends CreateRecord
{
    protected static string $resource = AttendanceRecordResource::class;

    protected static ?string $title = 'Enregistrer une présence';
}
