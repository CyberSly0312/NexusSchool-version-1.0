<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarTermResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarTermResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcademicCalendarTerms extends ListRecords
{
    protected static string $resource = AcademicCalendarTermResource::class;

    protected static ?string $title = 'Périodes';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
