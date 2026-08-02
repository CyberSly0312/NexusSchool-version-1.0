<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarYearResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarYearResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcademicCalendarYears extends ListRecords
{
    protected static string $resource = AcademicCalendarYearResource::class;

    protected static ?string $title = 'Années scolaires';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
