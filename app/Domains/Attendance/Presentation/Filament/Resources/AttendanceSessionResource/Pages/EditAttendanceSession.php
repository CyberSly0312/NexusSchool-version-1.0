<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources\AttendanceSessionResource\Pages;

use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceSession extends EditRecord
{
    protected static string $resource = AttendanceSessionResource::class;

    protected static ?string $title = 'Modifier une séance';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
