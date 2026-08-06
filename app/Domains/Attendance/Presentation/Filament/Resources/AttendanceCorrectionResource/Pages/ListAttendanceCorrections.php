<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources\AttendanceCorrectionResource\Pages;

use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceCorrectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceCorrections extends ListRecords
{
    protected static string $resource = AttendanceCorrectionResource::class;

    protected static ?string $title = 'Corrections';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
