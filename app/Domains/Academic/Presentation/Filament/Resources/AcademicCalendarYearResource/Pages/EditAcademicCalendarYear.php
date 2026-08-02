<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarYearResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarYearResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademicCalendarYear extends EditRecord
{
    protected static string $resource = AcademicCalendarYearResource::class;

    protected static ?string $title = 'Modifier une année scolaire';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
