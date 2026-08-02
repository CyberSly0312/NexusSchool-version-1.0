<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicSubjectResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicSubjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicSubject extends CreateRecord
{
    protected static string $resource = AcademicSubjectResource::class;

    protected static ?string $title = 'Créer une matière';
}
