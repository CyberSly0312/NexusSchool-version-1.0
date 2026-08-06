<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources\AttendanceCorrectionResource\Pages;

use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceCorrectionResource;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceCorrection extends EditRecord
{
    protected static string $resource = AttendanceCorrectionResource::class;

    protected static ?string $title = 'Modifier une correction';
}
