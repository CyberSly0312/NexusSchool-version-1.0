<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarTermResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarTermResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademicCalendarTerm extends EditRecord
{
    protected static string $resource = AcademicCalendarTermResource::class;

    protected static ?string $title = 'Modifier une période';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
