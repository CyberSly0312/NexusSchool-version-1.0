<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarTermResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarTermResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicCalendarTerm extends CreateRecord
{
    protected static string $resource = AcademicCalendarTermResource::class;

    protected static ?string $title = 'Créer une période';
}
