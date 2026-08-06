<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources\AttendanceSummaryResource\Pages;

use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceSummaryResource;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceSummaries extends ListRecords
{
    protected static string $resource = AttendanceSummaryResource::class;

    protected static ?string $title = 'Rapports de présence';
}
