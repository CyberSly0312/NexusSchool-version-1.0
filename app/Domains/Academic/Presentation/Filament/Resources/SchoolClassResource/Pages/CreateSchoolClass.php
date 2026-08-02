<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\SchoolClassResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\SchoolClassResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSchoolClass extends CreateRecord
{
    protected static string $resource = SchoolClassResource::class;

    protected static ?string $title = 'Créer une classe';
}
