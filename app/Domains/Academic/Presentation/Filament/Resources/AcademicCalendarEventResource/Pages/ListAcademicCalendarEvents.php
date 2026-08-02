<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarEventResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcademicCalendarEvents extends ListRecords
{
    protected static string $resource = AcademicCalendarEventResource::class;

    protected static ?string $title = 'Événements';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
