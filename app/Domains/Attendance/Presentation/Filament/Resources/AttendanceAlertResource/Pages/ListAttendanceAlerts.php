<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources\AttendanceAlertResource\Pages;

use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceAlertResource;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceAlerts extends ListRecords
{
    protected static string $resource = AttendanceAlertResource::class;

    protected static ?string $title = 'Alertes de présence';
}
