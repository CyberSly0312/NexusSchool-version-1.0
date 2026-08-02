<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicProgramResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicProgramResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicProgram extends CreateRecord
{
    protected static string $resource = AcademicProgramResource::class;

    protected static ?string $title = 'Créer un programme';
}
