<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\ClassSectionResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\ClassSectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassSection extends CreateRecord
{
    protected static string $resource = ClassSectionResource::class;

    protected static ?string $title = 'Créer une section';
}
