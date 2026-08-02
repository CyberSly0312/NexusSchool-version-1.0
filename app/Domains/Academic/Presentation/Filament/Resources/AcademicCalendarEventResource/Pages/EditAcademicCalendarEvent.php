<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarEventResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademicCalendarEvent extends EditRecord
{
    protected static string $resource = AcademicCalendarEventResource::class;

    protected static ?string $title = 'Modifier un événement';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
