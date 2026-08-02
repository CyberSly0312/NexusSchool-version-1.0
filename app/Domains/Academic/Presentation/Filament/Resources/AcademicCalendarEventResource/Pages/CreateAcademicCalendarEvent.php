<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarEventResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicCalendarEvent extends CreateRecord
{
    protected static string $resource = AcademicCalendarEventResource::class;

    protected static ?string $title = 'Créer un événement';
}
