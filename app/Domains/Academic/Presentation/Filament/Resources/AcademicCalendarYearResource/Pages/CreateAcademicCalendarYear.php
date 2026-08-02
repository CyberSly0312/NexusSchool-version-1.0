<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarYearResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarYearResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicCalendarYear extends CreateRecord
{
    protected static string $resource = AcademicCalendarYearResource::class;

    protected static ?string $title = 'Créer une année scolaire';
}
