<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\EducationLevelResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\EducationLevelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEducationLevel extends CreateRecord
{
    protected static string $resource = EducationLevelResource::class;

    protected static ?string $title = 'Créer un niveau';
}
